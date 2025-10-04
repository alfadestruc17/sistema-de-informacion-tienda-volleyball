<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['user', 'court'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function create()
    {
        $courts = Court::all();
        $users = User::where('role_id', '!=', 1)->get(); // Excluir admins

        return view('admin.reservations.create', compact('courts', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'court_id' => 'required|exists:courts,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion_horas' => 'required|integer|min:1|max:8',
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ]);

        $court = Court::find($request->court_id);

        // Verificar disponibilidad
        if (!Reservation::isSlotAvailable($request->court_id, $request->fecha, $request->hora_inicio, $request->duracion_horas)) {
            return redirect()->back()->withInput()->with('error', 'El horario seleccionado no está disponible');
        }

        // Calcular total estimado
        $totalEstimado = $court->precio_por_hora * $request->duracion_horas;

        Reservation::create([
            'user_id' => $request->user_id,
            'court_id' => $request->court_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'duracion_horas' => $request->duracion_horas,
            'estado' => $request->estado,
            'total_estimado' => $totalEstimado,
            'pagado_bool' => false,
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Reserva creada exitosamente');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'court', 'reservationItems']);

        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $reservation->load(['user', 'court']);
        $courts = Court::all();
        $users = User::where('role_id', '!=', 1)->get();

        return view('admin.reservations.edit', compact('reservation', 'courts', 'users'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'court_id' => 'required|exists:courts,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion_horas' => 'required|integer|min:1|max:8',
            'estado' => 'required|in:pendiente,confirmada,cancelada',
            'pagado_bool' => 'boolean',
        ]);

        // Si cambió la cancha, fecha u hora, verificar disponibilidad
        if ($request->court_id != $reservation->court_id ||
            $request->fecha != $reservation->fecha ||
            $request->hora_inicio != $reservation->hora_inicio ||
            $request->duracion_horas != $reservation->duracion_horas) {

            if (!Reservation::isSlotAvailable($request->court_id, $request->fecha, $request->hora_inicio, $request->duracion_horas, $reservation->id)) {
                return redirect()->back()->withInput()->with('error', 'El horario seleccionado no está disponible');
            }
        }

        $court = Court::find($request->court_id);
        $totalEstimado = $court->precio_por_hora * $request->duracion_horas;

        $reservation->update([
            'user_id' => $request->user_id,
            'court_id' => $request->court_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'duracion_horas' => $request->duracion_horas,
            'estado' => $request->estado,
            'total_estimado' => $totalEstimado,
            'pagado_bool' => $request->pagado_bool ?? false,
        ]);

        return redirect()->route('admin.reservations.index')->with('success', 'Reserva actualizada exitosamente');
    }

    public function destroy(Reservation $reservation)
    {
        // Solo permitir eliminar reservas canceladas o pendientes
        if ($reservation->estado === 'confirmada' && $reservation->pagado_bool) {
            return redirect()->back()->with('error', 'No se puede eliminar una reserva confirmada y pagada');
        }

        $reservation->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Reserva eliminada exitosamente');
    }
}
