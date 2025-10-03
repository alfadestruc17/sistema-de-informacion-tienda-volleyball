<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CourtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Court::all());
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
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'precio_por_hora' => 'required|numeric',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $court = Court::create($request->all());
        return response()->json($court, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    public function availability(Request $request)
    {
        $request->validate([
            'court_id' => 'nullable|exists:courts,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $courtId = $request->court_id;
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        // Obtener todas las reservas en el rango de fechas
        $query = Reservation::whereBetween('fecha', [$dateFrom, $dateTo])
            ->where('estado', '!=', 'cancelada');

        if ($courtId) {
            $query->where('court_id', $courtId);
        }

        $reservations = $query->with(['court', 'user'])->get();

        $availability = [];

        // Generar disponibilidad por día
        for ($date = $dateFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            $dayReservations = $reservations->where('fecha', $date->toDateString());

            $occupiedSlots = [];
            $availableSlots = [];

            // Asumir horario de 8:00 a 22:00 con franjas de 1 hora
            for ($hour = 8; $hour < 22; $hour++) {
                $slotStart = Carbon::createFromTime($hour, 0);
                $slotEnd = Carbon::createFromTime($hour + 1, 0);

                $isOccupied = false;
                foreach ($dayReservations as $reservation) {
                    $resStart = Carbon::parse($reservation->hora_inicio);
                    $resEnd = $resStart->copy()->addHours($reservation->duracion_horas);

                    // Verificar solapamiento
                    if ($slotStart < $resEnd && $slotEnd > $resStart) {
                        $isOccupied = true;
                        break;
                    }
                }

                if ($isOccupied) {
                    $occupiedSlots[] = [
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'reservation' => $dayReservations->first(function($res) use ($slotStart, $slotEnd) {
                            $resStart = Carbon::parse($res->hora_inicio);
                            $resEnd = $resStart->copy()->addHours($res->duracion_horas);
                            return $slotStart < $resEnd && $slotEnd > $resStart;
                        })
                    ];
                } else {
                    $availableSlots[] = [
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                    ];
                }
            }

            $availability[] = [
                'date' => $date->toDateString(),
                'court_id' => $courtId,
                'available_slots' => $availableSlots,
                'occupied_slots' => $occupiedSlots,
            ];
        }

        return response()->json([
            'availability' => $availability,
            'courts' => $courtId ? Court::find($courtId) : Court::all(),
        ]);
    }
}
