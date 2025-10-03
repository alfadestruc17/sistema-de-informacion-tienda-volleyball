<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'reservation_id',
        'total',
        'estado_pago',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'estado_pago' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal(): float
    {
        return $this->orderItems->sum(function ($item) {
            return $item->cantidad * $item->precio_unitario;
        });
    }
}
