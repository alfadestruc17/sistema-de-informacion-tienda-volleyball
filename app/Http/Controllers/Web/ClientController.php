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
        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion_horas' => 'required|integer|min:1|max:3',
        ]);

        $court = Court::find($request->court_id);

        // Verificar disponibilidad
        if (!$this->isTimeSlotAvailable($court, $request->fecha, $request->hora_inicio, $request->duracion_horas)) {
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

        // Verificar conflictos con otras reservas
        $conflictingReservations = Reservation::where('court_id', $court->id)
            ->where('fecha', $date)
            ->where('estado', '!=', 'cancelada')
            ->get();

        foreach ($conflictingReservations as $reservation) {
            $resStart = Carbon::parse($date . ' ' . $reservation->hora_inicio);
            $resEnd = $resStart->copy()->addHours((int) $reservation->duracion_horas);

            // Verificar superposición
            if ($startDateTime < $resEnd && $endDateTime > $resStart) {
                return false;
            }
        }

        return true;
    }
}