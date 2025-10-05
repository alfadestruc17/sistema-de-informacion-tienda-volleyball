<?php

namespace App\Http\Controllers\Web;


use App\Models\Court;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Solo aplicar restricción de cliente a métodos específicos
            $restrictedMethods = ['reservations', 'createReservation', 'payReservation', 'cancelReservation'];

            if (in_array($request->route()->getActionMethod(), $restrictedMethods)) {
                $user = Auth::user();
                if (!$user->role || $user->role->nombre !== 'cliente') {
                    abort(403, 'Acceso denegado - Solo para clientes');
                }
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        // Dashboard simple para clientes - redirige al calendario
        return redirect()->route('calendar.index');
    }

    public function calendar(Request $request)
    {
        $courts = Court::where('estado', 'activo')->get();
        $selectedCourt = null;
        $currentWeek = $this->getCurrentWeek();
        $availability = [];

        if ($request->has('court_id')) {
            $selectedCourt = Court::find($request->court_id);

            // Calcular disponibilidad para cada slot
            if ($selectedCourt) {
                foreach ($currentWeek['days'] as $day) {
                    foreach (['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'] as $hour) {
                        $availability[$day['date']][$hour] = $this->isTimeSlotAvailable($selectedCourt, $day['date'], $hour);
                    }
                }
            }
        }

        $hours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];

        return view('calendar.index', compact('courts', 'selectedCourt', 'currentWeek', 'hours', 'availability'));
    }

    public function reservations()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->with('court')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        return view('client.reservations', compact('reservations'));
    }

    public function createReservation(Request $request)
    {
        Log::info('Intentando crear reserva', [
            'court_id' => $request->court_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'duracion_horas' => $request->duracion_horas,
            'user_id' => Auth::id()
        ]);

        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion_horas' => 'required|integer|min:1|max:3',
        ]);

        $court = Court::find($request->court_id);
        Log::info('Cancha encontrada', ['court' => $court->toArray()]);

        // Verificar disponibilidad
        $isAvailable = $this->isTimeSlotAvailable($court, $request->fecha, $request->hora_inicio, $request->duracion_horas);
        Log::info('Verificación de disponibilidad', [
            'court_id' => $court->id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'duracion_horas' => $request->duracion_horas,
            'is_available' => $isAvailable
        ]);

        if (!$isAvailable) {
            Log::warning('Horario no disponible', [
                'court_id' => $court->id,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio
            ]);
            return back()->with('error', 'El horario seleccionado no está disponible');
        }

        // Calcular total
        $total = $court->precio_por_hora * (int) $request->duracion_horas;

        // Crear reserva
        try {
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'court_id' => $request->court_id,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'duracion_horas' => (int) $request->duracion_horas,
                'estado' => 'confirmada',
                'total_estimado' => $total,
                'pagado_bool' => false,
            ]);

            Log::info('Reserva creada exitosamente', ['reservation_id' => $reservation->id]);

            return redirect()->route('calendar.index')->with('success', '¡Reserva creada exitosamente! La cancha ha sido reservada para la fecha y hora seleccionada.');
        } catch (\Exception $e) {
            Log::error('Error al crear reserva', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return back()->with('error', 'Error al crear la reserva: ' . $e->getMessage());
        }
    }

    public function payReservation(Reservation $reservation)
    {
        // Verificar que la reserva pertenece al usuario
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para esta reserva');
        }

        $reservation->update(['pagado' => true]);

        return back()->with('success', 'Pago procesado exitosamente');
    }

    public function cancelReservation(Reservation $reservation)
    {
        // Verificar que la reserva pertenece al usuario
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para esta reserva');
        }

        // Solo permitir cancelar reservas que no han pasado
        $reservationDateTime = Carbon::parse($reservation->fecha . ' ' . $reservation->hora_inicio);
        if ($reservationDateTime->isPast()) {
            return back()->with('error', 'No puedes cancelar una reserva que ya ha pasado');
        }

        $reservation->update(['estado' => 'cancelada']);

        return back()->with('success', 'Reserva cancelada exitosamente');
    }

    private function getCurrentWeek()
    {
        $startOfWeek = Carbon::now()->startOfWeek(); // Lunes
        $endOfWeek = Carbon::now()->endOfWeek(); // Domingo

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $days[] = [
                'name' => $date->locale('es')->dayName,
                'date' => $date->format('Y-m-d'),
            ];
        }

        return [
            'start' => $startOfWeek->format('d/m/Y'),
            'end' => $endOfWeek->format('d/m/Y'),
            'days' => $days,
        ];
    }

    public function isTimeSlotAvailable($court, $date, $startTime, $duration = 1)
    {
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $startTime);
        $endDateTime = $startDateTime->copy()->addHours((int) $duration);

        Log::info('Verificando disponibilidad', [
            'court_id' => $court->id,
            'date' => $date,
            'start_time' => $startTime,
            'duration' => $duration,
            'requested_start' => $startDateTime->format('Y-m-d H:i'),
            'requested_end' => $endDateTime->format('Y-m-d H:i')
        ]);

        // Verificar conflictos con otras reservas
        $conflictingReservations = Reservation::where('court_id', $court->id)
            ->where('fecha', $date)
            ->where('estado', '!=', 'cancelada')
            ->get();

        Log::info('Reservas existentes para esta cancha y fecha', [
            'court_id' => $court->id,
            'date' => $date,
            'reservations_count' => $conflictingReservations->count(),
            'reservations' => $conflictingReservations->map(function($r) {
                return [
                    'id' => $r->id,
                    'hora_inicio' => $r->hora_inicio,
                    'duracion_horas' => $r->duracion_horas,
                    'estado' => $r->estado
                ];
            })->toArray()
        ]);

        foreach ($conflictingReservations as $reservation) {
            $resStart = Carbon::parse($date . ' ' . $reservation->hora_inicio);
            $resEnd = $resStart->copy()->addHours((int) $reservation->duracion_horas);

            Log::info('Verificando conflicto con reserva existente', [
                'existing_reservation_id' => $reservation->id,
                'existing_start' => $resStart->format('Y-m-d H:i'),
                'existing_end' => $resEnd->format('Y-m-d H:i'),
                'overlap' => ($startDateTime < $resEnd && $endDateTime > $resStart)
            ]);

            // Verificar superposición
            if ($startDateTime < $resEnd && $endDateTime > $resStart) {
                Log::warning('Conflicto detectado', [
                    'court_id' => $court->id,
                    'requested_slot' => $startDateTime->format('Y-m-d H:i') . ' - ' . $endDateTime->format('Y-m-d H:i'),
                    'conflicting_reservation' => $reservation->id,
                    'conflicting_slot' => $resStart->format('Y-m-d H:i') . ' - ' . $resEnd->format('Y-m-d H:i')
                ]);
                return false;
            }
        }

        Log::info('Slot disponible', [
            'court_id' => $court->id,
            'date' => $date,
            'time' => $startTime,
            'duration' => $duration
        ]);
        return true;
    }
}