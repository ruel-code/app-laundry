<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota {{ $order->id }}</title>
    <style>
        @page { margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            font-size: 9px;
            margin: 2px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .info {
            font-size: 9px;
            margin-bottom: 6px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 3px 2px;
            text-align: left;
        }
        table td {
            padding: 2px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-section {
            margin-top: 6px;
            font-size: 10px;
        }
        .total-section p {
            margin: 2px 0;
        }
        .grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 9px;
        }
        .status {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin: 6px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAUNDRY AL-INSYIROH</h1>
        <p>"Cuci Bersih, Harga Hemat"</p>
    </div>

    <div class="divider"></div>

    <div class="info">
        <p>Pesanan #: {{ $order->id }}</p>
        <p>Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p>Kasir: {{ $order->user->name }}</p>
        <p>Santri: {{ $order->santri->name }}</p>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Jenis Pakaian</th>
                <th class="text-center" style="width: 30px;">Jml</th>
                <th class="text-right" style="width: 40px;">Berat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->item_name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format((float) $item->weight_kg, 1) }} kg</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">-</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="total-section">
        <p>Total: {{ number_format((float) $order->weight_kg, 1) }} kg</p>
        <p>(dibulatkan: {{ $roundedWeight }} kg)</p>
        <p>Harga: Rp{{ number_format($pricePerKg) }}/kg</p>

        @if ((float) $order->discount_kg > 0)
        <p>Diskon: {{ $order->discount_kg }} kg (gratis)</p>
        @endif

        <p class="grand-total">
            Total: Rp{{ number_format((int) $order->total_price) }}
        </p>
    </div>

    <div class="status">
        Status: {{ strtoupper(str_replace('_', ' ', $order->payment_status)) }}
    </div>

    <div class="divider"></div>

    <div class="footer">
        <p>Terima kasih telah menggunakan</p>
        <p>jasa Laundry Al-Insyiroh</p>
    </div>
</body>
</html>
