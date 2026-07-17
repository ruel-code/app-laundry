<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_name',
        'quantity',
        'weight_kg',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'weight_kg' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
