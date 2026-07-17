<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Santri;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = Carbon::today();

        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $totalRevenueToday = Order::whereDate('created_at', $today)->sum('total_price');
        $newSantriToday = Santri::whereDate('created_at', $today)->count();
        $activePromo = Order::whereDate('created_at', $today)->where('discount_kg', '>', 0)->count();

        $recentOrders = Order::with('santri')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'santri_name' => $o->santri->name,
                'weight_kg' => number_format((float) $o->weight_kg, 2),
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'created_at' => $o->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders_today' => $totalOrdersToday,
                'total_revenue_today' => (int) $totalRevenueToday,
                'new_santri_today' => $newSantriToday,
                'active_promo' => $activePromo,
                'recent_orders' => $recentOrders,
            ],
            'message' => 'Data dashboard',
        ]);
    }
}
