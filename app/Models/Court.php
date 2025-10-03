<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio_por_hora', 'estado'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
