<?php

namespace Database\Seeders;

use App\Models\LoyaltyLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Santri;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@laundry.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $santri = collect([
            ['name' => 'Ahmad Hidayat', 'kamar' => 'B-12', 'devisi' => 'Al-Fatih', 'total_weight' => 12.0],
            ['name' => 'Muhammad Rizky', 'kamar' => 'A-05', 'devisi' => 'Al-Ghazali', 'total_weight' => 8.5],
            ['name' => 'Zaidan Mukhtar', 'kamar' => 'C-08', 'devisi' => 'Ibn Sina', 'total_weight' => 4.2],
            ['name' => 'Imron Rosyadi', 'kamar' => 'D-02', 'devisi' => 'Al-Fatih', 'total_weight' => 6.8],
            ['name' => 'Faisal Ramadhan', 'kamar' => 'A-03', 'devisi' => 'Ar-Razi', 'total_weight' => 10.0],
            ['name' => 'Zulfikar Ali', 'kamar' => 'C-12', 'devisi' => 'Al-Ghazali', 'total_weight' => 3.5],
            ['name' => 'Umar Mukhtar', 'kamar' => 'B-07', 'devisi' => 'Ibn Sina', 'total_weight' => 7.0],
            ['name' => 'Hasan Basri', 'kamar' => 'A-09', 'devisi' => 'Al-Fatih', 'total_weight' => 2.0],
            ['name' => 'Fatimah Syams', 'kamar' => '1A', 'devisi' => 'Al-Ghazali', 'total_weight' => 9.5],
            ['name' => 'Zaki Al-Farabi', 'kamar' => '2B', 'devisi' => 'Ar-Razi', 'total_weight' => 5.0],
            ['name' => 'Ahmad Fauzi', 'kamar' => 'B-12', 'devisi' => 'Al-Fatih', 'total_weight' => 15.0],
            ['name' => 'Siti Aminah', 'kamar' => '2A', 'devisi' => 'Al-Ghazali', 'total_weight' => 6.0],
        ])->map(fn ($s) => Santri::create($s));

        $statuses = ['dicuci', 'dijemur', 'dilipat', 'dikemas', 'selesai'];
        $paymentStatuses = ['belum_bayar', 'lunas'];
        $itemPool = [
            ['item_name' => 'Baju', 'quantity' => 3, 'weight_kg' => 0.5],
            ['item_name' => 'Celana', 'quantity' => 2, 'weight_kg' => 0.8],
            ['item_name' => 'Sarung', 'quantity' => 2, 'weight_kg' => 0.6],
            ['item_name' => 'Baju', 'quantity' => 4, 'weight_kg' => 1.0],
            ['item_name' => 'Lainnya', 'quantity' => 1, 'weight_kg' => 0.3],
            ['item_name' => 'Celana', 'quantity' => 1, 'weight_kg' => 0.4],
            ['item_name' => 'Sarung', 'quantity' => 3, 'weight_kg' => 0.9],
            ['item_name' => 'Baju', 'quantity' => 5, 'weight_kg' => 1.2],
        ];

        $today = Carbon::today();

        for ($i = 0; $i < 25; $i++) {
            $s = $santri->random();
            $weight = round(1.0 + mt_rand(5, 40) / 10, 1);
            $roundedWeight = (int) floor($weight);
            $pricePerKg = 3000;
            $discountKg = $s->total_weight + $roundedWeight >= 10 ? 1 : 0;
            $chargeableKg = max(0, $roundedWeight - $discountKg);
            $totalPrice = $chargeableKg * $pricePerKg;
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $createdAt = $today->copy()->subDays(mt_rand(0, 6))->addHours(mt_rand(7, 17))->addMinutes(mt_rand(0, 59));

            $order = Order::create([
                'santri_id' => $s->id,
                'user_id' => $user->id,
                'weight_kg' => $weight,
                'total_price' => $totalPrice,
                'item_details' => null,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'discount_kg' => $discountKg,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $numItems = mt_rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $item = $itemPool[array_rand($itemPool)];
                $order->items()->create([
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'weight_kg' => round($item['weight_kg'] / $numItems, 2),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            if ($discountKg > 0) {
                $newAccumulation = $s->fresh()->total_weight + $roundedWeight;
                $order->loyaltyLogs()->create([
                    'santri_id' => $s->id,
                    'free_kg' => $discountKg,
                    'total_accumulated' => $newAccumulation,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $s->increment('total_weight', $roundedWeight);
            } else {
                $s->increment('total_weight', $roundedWeight);
            }
        }
    }
}
