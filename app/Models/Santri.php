<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'santri';

    protected $fillable = [
        'name',
        'kamar',
        'devisi',
        'total_weight',
    ];

    protected function casts(): array
    {
        return [
            'total_weight' => 'decimal:2',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function loyaltyLogs()
    {
        return $this->hasMany(LoyaltyLog::class);
    }
}
