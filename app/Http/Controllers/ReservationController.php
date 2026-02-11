<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Reservation\CreateReservationRequest;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService
    ) {
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (in_array($user->role->nombre, ['admin', 'cajero'])) {
            $reservations = $this->reservationService->getAll();
        } else {
            $reservations = $this->reservationService->getByUser($user->id);
        }

        return response()->json($reservations);
    }

    public function store(CreateReservationRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$this->reservationService->isSlotAvailable(
            (int) $request->court_id,
            $request->fecha,
            $request->hora_inicio,
            (int) $request->duracion_horas
        )) {
            return response()->json(['message' => 'El horario solicitado no está disponible'], 409);
        }

        $reservation = $this->reservationService->createReservation([
            'user_id' => $user->id,
            'court_id' => $request->court_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'duracion_horas' => $request->duracion_horas,
            'estado' => 'pendiente',
            'pagado_bool' => false,
        ]);

        return response()->json($reservation->load(['user', 'court']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $reservation = Reservation::with(['user', 'court', 'reservationItems'])->findOrFail($id);
        $user = Auth::user();

        if ($reservation->user_id !== $user->id && !in_array($user->role->nombre, ['admin', 'cajero'])) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($reservation);
    }

    public function destroy(string $id): JsonResponse
    {
        $reservation = $this->reservationService->find((int) $id);
        if (!$reservation) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $user = Auth::user();
        if ($reservation->user_id !== $user->id && !in_array($user->role->nombre, ['admin', 'cajero'])) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($reservation->pagado_bool) {
            return response()->json(['message' => 'No se puede cancelar una reserva pagada'], 400);
        }

        $this->reservationService->cancelReservation($reservation);
        return response()->json(['message' => 'Reserva cancelada exitosamente']);
    }
}
