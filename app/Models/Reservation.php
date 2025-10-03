<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'court_id',
        'fecha',
        'hora_inicio',
        'duracion_horas',
        'estado',
        'total_estimado',
        'pagado_bool'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'total_estimado' => 'decimal:2',
        'pagado_bool' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function isSlotAvailable($courtId, $fecha, $horaInicio, $duracionHoras)
    {
        $startTime = Carbon::parse($horaInicio);
        $endTime = $startTime->copy()->addHours($duracionHoras);

        $overlappingReservations = self::where('court_id', $courtId)
            ->where('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->get()
            ->filter(function ($reservation) use ($startTime, $endTime) {
                $resStart = Carbon::parse($reservation->hora_inicio);
                $resEnd = $resStart->copy()->addHours($reservation->duracion_horas);

                // Verificar solapamiento
                return $startTime < $resEnd && $endTime > $resStart;
            });

        return $overlappingReservations->isEmpty();
    }
}
