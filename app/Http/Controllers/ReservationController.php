<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Si es admin o cajero, ver todas las reservas
        if (in_array($user->role->nombre, ['admin', 'cajero'])) {
            $reservations = Reservation::with(['user', 'court'])->get();
        } else {
            // Si es cliente, solo ver sus reservas
            $reservations = $user->reservations()->with('court')->get();
        }

        return response()->json($reservations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion_horas' => 'required|integer|min:1|max:8',
        ]);

        $user = Auth::user();

        // Verificar disponibilidad
        if (!Reservation::isSlotAvailable(
            $request->court_id,
            $request->fecha,
            $request->hora_inicio,
            $request->duracion_horas
        )) {
            return response()->json(['message' => 'El horario solicitado no está disponible'], 409);
        }

        // Calcular total estimado
        $court = \App\Models\Court::find($request->court_id);
        $totalEstimado = $court->precio_por_hora * $request->duracion_horas;

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'court_id' => $request->court_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'duracion_horas' => $request->duracion_horas,
            'estado' => 'pendiente',
            'total_estimado' => $totalEstimado,
            'pagado_bool' => false,
        ]);

        return response()->json($reservation->load(['user', 'court']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['user', 'court', 'reservationItems'])->findOrFail($id);

        $user = Auth::user();

        // Verificar permisos: solo el propietario o admin/cajero pueden ver
        if ($reservation->user_id !== $user->id && !in_array($user->role->nombre, ['admin', 'cajero'])) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($reservation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $user = Auth::user();

        // Verificar permisos: solo el propietario o admin/cajero pueden cancelar
        if ($reservation->user_id !== $user->id && !in_array($user->role->nombre, ['admin', 'cajero'])) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Solo permitir cancelar si no está pagada o es reciente
        if ($reservation->pagado_bool) {
            return response()->json(['message' => 'No se puede cancelar una reserva pagada'], 400);
        }

        $reservation->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Reserva cancelada exitosamente']);
    }
}
