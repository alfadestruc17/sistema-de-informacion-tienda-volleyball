<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\UpdateReservationRequest;
use App\Models\Reservation;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function index(): View
    {
        $reservations = $this->reservationService->paginate(15);
        return view('admin.reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        $courts = \App\Models\Court::all();
        $users = $this->userRepository->getNonAdmins();
        return view('admin.reservations.create', compact('courts', 'users'));
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (!$this->reservationService->isSlotAvailable(
            (int) $data['court_id'],
            $data['fecha'],
            $data['hora_inicio'],
            (int) $data['duracion_horas']
        )) {
            return redirect()->back()->withInput()->with('error', 'El horario seleccionado no está disponible');
        }

        $this->reservationService->createReservation(array_merge($data, ['pagado_bool' => false]));
        return redirect()->route('admin.reservations.index')->with('success', 'Reserva creada exitosamente');
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['user', 'court', 'reservationItems']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation): View
    {
        $reservation->load(['user', 'court']);
        $courts = \App\Models\Court::all();
        $users = $this->userRepository->getNonAdmins();
        return view('admin.reservations.edit', compact('reservation', 'courts', 'users'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validated();

        $slotChanged = $data['court_id'] != $reservation->court_id
            || $data['fecha'] != $reservation->fecha->format('Y-m-d')
            || $data['hora_inicio'] != $reservation->hora_inicio
            || $data['duracion_horas'] != $reservation->duracion_horas;

        if ($slotChanged && !$this->reservationService->isSlotAvailable(
            (int) $data['court_id'],
            $data['fecha'],
            $data['hora_inicio'],
            (int) $data['duracion_horas'],
            $reservation->id
        )) {
            return redirect()->back()->withInput()->with('error', 'El horario seleccionado no está disponible');
        }

        $this->reservationService->updateReservation($reservation, $data);
        return redirect()->route('admin.reservations.index')->with('success', 'Reserva actualizada exitosamente');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        if (!$this->reservationService->canDelete($reservation)) {
            return redirect()->back()->with('error', 'No se puede eliminar una reserva confirmada y pagada');
        }
        $this->reservationService->delete($reservation);
        return redirect()->route('admin.reservations.index')->with('success', 'Reserva eliminada exitosamente');
    }
}
