<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $request): JsonResponse
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $orders = Order::with('santri')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_weight' => number_format((float) $orders->sum('weight_kg'), 2),
            'total_revenue' => (int) $orders->sum('total_price'),
            'total_discount_kg' => number_format((float) $orders->sum('discount_kg'), 2),
            'total_discount_value' => (int) ($orders->sum('discount_kg') * 3000),
            'net_revenue' => (int) ($orders->sum('total_price')),
        ];

        $data = $orders->map(fn ($o) => [
            'id' => $o->id,
            'santri_name' => $o->santri->name,
            'weight_kg' => number_format((float) $o->weight_kg, 2),
            'total_price' => (int) $o->total_price,
            'status' => $o->status,
            'discount_kg' => number_format((float) $o->discount_kg, 2),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'summary' => $summary,
                'orders' => $data,
            ],
            'message' => 'Laporan harian',
        ]);
    }

    public function monthly(Request $request): JsonResponse
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $orders = Order::with('santri')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->get();

        $totalRevenue = (int) $orders->sum('total_price');
        $totalDiscountKg = (float) $orders->sum('discount_kg');
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        $summary = [
            'total_orders' => $orders->count(),
            'total_weight' => number_format((float) $orders->sum('weight_kg'), 2),
            'total_revenue' => $totalRevenue,
            'total_discount_kg' => number_format($totalDiscountKg, 2),
            'total_discount_value' => (int) ($totalDiscountKg * 3000),
            'net_revenue' => $totalRevenue,
            'avg_daily_orders' => round($orders->count() / $daysInMonth, 1),
            'avg_daily_revenue' => $daysInMonth > 0 ? round($totalRevenue / $daysInMonth) : 0,
        ];

        $data = $orders->map(fn ($o) => [
            'id' => $o->id,
            'santri_name' => $o->santri->name,
            'weight_kg' => number_format((float) $o->weight_kg, 2),
            'total_price' => (int) $o->total_price,
            'status' => $o->status,
            'created_at' => $o->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'month' => (int) $month,
                'year' => (int) $year,
                'summary' => $summary,
                'orders' => $data,
            ],
            'message' => 'Laporan bulanan',
        ]);
    }
}
