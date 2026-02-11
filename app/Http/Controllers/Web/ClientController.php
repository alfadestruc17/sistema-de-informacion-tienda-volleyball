<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Requests\Reservation\CreateReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
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

    public function dashboard(): RedirectResponse
    {
        return redirect()->route('calendar.index');
    }

    public function calendar(\Illuminate\Http\Request $request): View
    {
        $courts = \App\Models\Court::where('estado', 'activo')->get();
        $selectedCourt = null;
        $currentWeek = $this->getCurrentWeek();
        $availability = [];

        if ($request->has('court_id')) {
            $selectedCourt = \App\Models\Court::find($request->court_id);
            if ($selectedCourt) {
                foreach ($currentWeek['days'] as $day) {
                    foreach (['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'] as $hour) {
                        $availability[$day['date']][$hour] = $this->reservationService->isSlotAvailable(
                            $selectedCourt->id,
                            $day['date'],
                            $hour,
                            1
                        );
                    }
                }
            }
        }

        $hours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
        return view('calendar.index', compact('courts', 'selectedCourt', 'currentWeek', 'hours', 'availability'));
    }

    public function reservations(): View
    {
        $reservations = $this->reservationService->getByUser((int) Auth::id());
        return view('client.reservations', compact('reservations'));
    }

    public function createReservation(CreateReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (!$this->reservationService->isSlotAvailable(
            (int) $data['court_id'],
            $data['fecha'],
            $data['hora_inicio'],
            (int) $data['duracion_horas']
        )) {
            return back()->with('error', 'El horario seleccionado no está disponible');
        }

        $this->reservationService->createReservation([
            'user_id' => Auth::id(),
            'court_id' => $data['court_id'],
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'duracion_horas' => (int) $data['duracion_horas'],
            'estado' => 'confirmada',
            'pagado_bool' => false,
        ]);

        return redirect()->route('calendar.index')->with('success', '¡Reserva creada exitosamente! La cancha ha sido reservada para la fecha y hora seleccionada.');
    }

    public function payReservation(Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para esta reserva');
        }
        $this->reservationService->markAsPaid($reservation);
        return back()->with('success', 'Pago procesado exitosamente');
    }

    public function cancelReservation(Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para esta reserva');
        }
        $reservationDateTime = Carbon::parse($reservation->fecha->format('Y-m-d') . ' ' . $reservation->hora_inicio);
        if ($reservationDateTime->isPast()) {
            return back()->with('error', 'No puedes cancelar una reserva que ya ha pasado');
        }
        $this->reservationService->cancelReservation($reservation);
        return back()->with('success', 'Reserva cancelada exitosamente');
    }

    /**
     * @return array{start: string, end: string, days: array<int, array{name: string, date: string}>}
     */
    private function getCurrentWeek(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
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
}
