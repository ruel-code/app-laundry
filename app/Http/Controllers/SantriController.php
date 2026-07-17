<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SantriController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Santri::query();

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $santri = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $santri->items(),
            'message' => 'Daftar santri',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'kamar' => 'nullable|string|max:50',
            'devisi' => 'nullable|string|max:100',
        ]);

        $santri = Santri::create($validated);

        return response()->json([
            'success' => true,
            'data' => $santri,
            'message' => 'Santri berhasil ditambahkan',
        ], 201);
    }

    public function show(Santri $santri): JsonResponse
    {
        $santri->load('orders');

        return response()->json([
            'success' => true,
            'data' => $santri,
            'message' => 'Detail santri',
        ]);
    }

    public function update(Request $request, Santri $santri): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'kamar' => 'nullable|string|max:50',
            'devisi' => 'nullable|string|max:100',
        ]);

        $santri->update($validated);

        return response()->json([
            'success' => true,
            'data' => $santri,
            'message' => 'Santri berhasil diupdate',
        ]);
    }

    public function destroy(Santri $santri): JsonResponse
    {
        $santri->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Santri berhasil dihapus',
        ]);
    }
}
