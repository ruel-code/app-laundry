<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'santri_id',
        'user_id',
        'weight_kg',
        'total_price',
        'item_details',
        'status',
        'payment_status',
        'discount_kg',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
            'discount_kg' => 'decimal:2',
            'item_details' => 'array',
        ];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function loyaltyLogs()
    {
        return $this->hasMany(LoyaltyLog::class);
    }
}
