<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
        'precio',
        'stock',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function reduceStock(int $quantity): void
    {
        if (!$this->hasStock($quantity)) {
            throw new \Exception("Insufficient stock for product {$this->nombre}");
        }
        $this->decrement('stock', $quantity);
    }

    public function addStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}
