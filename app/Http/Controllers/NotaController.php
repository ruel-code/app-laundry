<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaController extends Controller
{
    public function __invoke(Order $order)
    {
        $order->load('santri', 'items');

        $roundedWeight = (int) floor((float) $order->weight_kg);
        $pricePerKg = 3000;

        $pdf = Pdf::loadView('pdf.nota', compact('order', 'roundedWeight', 'pricePerKg'));
        $pdf->setPaper([0, 0, 226.77, 800], 'portrait');

        return $pdf->download("nota-{$order->id}.pdf");
    }
}
