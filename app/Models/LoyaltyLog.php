<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyLog extends Model
{
    protected $table = 'loyalty_logs';

    protected $fillable = [
        'santri_id',
        'order_id',
        'free_kg',
        'total_accumulated',
    ];

    protected function casts(): array
    {
        return [
            'free_kg' => 'decimal:2',
            'total_accumulated' => 'decimal:2',
        ];
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
