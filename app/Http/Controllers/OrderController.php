<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Santri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('santri');

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($date = $request->date) {
            $query->whereDate('created_at', $date);
        }

        $orders = $query->latest()->paginate(15);

        $data = $orders->map(fn ($o) => [
            'id' => $o->id,
            'santri_name' => $o->santri->name,
            'weight_kg' => number_format((float) $o->weight_kg, 2),
            'total_price' => (int) $o->total_price,
            'status' => $o->status,
            'payment_status' => $o->payment_status,
            'discount_kg' => number_format((float) $o->discount_kg, 2),
            'created_at' => $o->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $data,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                ],
            ],
            'message' => 'Daftar pesanan',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'weight_kg' => 'required|numeric|min:1',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.weight_kg' => 'required|numeric|min:0',
            'payment_status' => 'sometimes|in:belum_bayar,lunas',
        ]);

        $santri = Santri::findOrFail($validated['santri_id']);

        $roundedWeight = (int) floor($validated['weight_kg']);
        $pricePerKg = 3000;
        $discountKg = 0;

        $newAccumulation = $santri->total_weight + $roundedWeight;

        if ($newAccumulation >= 10) {
            $discountKg = 1;
        }

        $chargeableKg = max(0, $roundedWeight - $discountKg);
        $totalPrice = $chargeableKg * $pricePerKg;

        $order = Order::create([
            'santri_id' => $validated['santri_id'],
            'user_id' => $request->user()->id,
            'weight_kg' => $validated['weight_kg'],
            'total_price' => $totalPrice,
            'item_details' => $validated['items'] ?? null,
            'discount_kg' => $discountKg,
            'status' => 'dicuci',
            'payment_status' => $validated['payment_status'] ?? 'belum_bayar',
        ]);

        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }
        }

        $santri->increment('total_weight', $roundedWeight);

        if ($discountKg > 0) {
            $order->loyaltyLogs()->create([
                'santri_id' => $santri->id,
                'free_kg' => $discountKg,
                'total_accumulated' => $santri->fresh()->total_weight,
            ]);
        }

        $order->load('santri', 'items');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'santri_id' => $order->santri_id,
                'santri_name' => $order->santri->name,
                'weight_kg' => number_format((float) $order->weight_kg, 2),
                'rounded_weight' => $roundedWeight,
                'total_price' => (int) $totalPrice,
                'discount_kg' => number_format((float) $discountKg, 2),
                'promo_applied' => $discountKg > 0,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'items' => $order->items->map(fn ($i) => [
                    'item_name' => $i->item_name,
                    'quantity' => $i->quantity,
                    'weight_kg' => number_format((float) $i->weight_kg, 2),
                ]),
            ],
            'message' => 'Pesanan berhasil dibuat',
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load('santri', 'user', 'items');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'santri' => [
                    'id' => $order->santri->id,
                    'name' => $order->santri->name,
                ],
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                ],
                'weight_kg' => number_format((float) $order->weight_kg, 2),
                'rounded_weight' => (int) floor((float) $order->weight_kg),
                'total_price' => (int) $order->total_price,
                'discount_kg' => number_format((float) $order->discount_kg, 2),
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'items' => $order->items->map(fn ($i) => [
                    'item_name' => $i->item_name,
                    'quantity' => $i->quantity,
                    'weight_kg' => number_format((float) $i->weight_kg, 2),
                ]),
                'created_at' => $order->created_at->toIso8601String(),
            ],
            'message' => 'Detail pesanan',
        ]);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:dicuci,dijemur,dilipat,dikemas,selesai',
            'payment_status' => 'sometimes|in:belum_bayar,lunas',
            'weight_kg' => 'sometimes|numeric|min:1',
        ]);

        if (isset($validated['status'])) {
            $validOrder = ['dicuci', 'dijemur', 'dilipat', 'dikemas', 'selesai'];
            $currentIdx = array_search($order->status, $validOrder);
            $newIdx = array_search($validated['status'], $validOrder);

            if ($newIdx !== $currentIdx + 1) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Status tidak valid, hanya bisa maju 1 langkah',
                ], 422);
            }
        }

        $order->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'updated_at' => $order->updated_at->toIso8601String(),
            ],
            'message' => 'Pesanan berhasil diupdate',
        ]);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Pesanan berhasil dihapus',
        ]);
    }
}
