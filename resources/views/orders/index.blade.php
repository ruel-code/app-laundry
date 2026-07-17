@extends('components.layout', ['activeNav' => 'orders'])

@section('title', 'Orders - Laundry Al-Insyiroh')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Order Management</h2>
        <p class="text-on-surface-variant font-body-md">Track and manage all laundry orders.</p>
    </div>
    <a href="/orders/create" class="bg-primary hover:opacity-90 text-on-primary font-label-sm text-label-sm px-4 py-2.5 rounded-lg flex items-center gap-2 transition-all">
        <span class="material-symbols-outlined text-[18px]">add</span>
        New Order
    </a>
</div>

<section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="clinical-card p-6">
        <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">ACTIVE ORDERS</p>
        <p class="font-data-table text-headline-lg text-primary" id="statActive">-</p>
    </div>
    <div class="clinical-card p-6">
        <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">TOTAL WEIGHT</p>
        <p class="font-data-table text-headline-lg text-secondary" id="statWeight">- <span class="text-body-md font-body-md">kg</span></p>
    </div>
    <div class="clinical-card p-6">
        <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">COMPLETED TODAY</p>
        <p class="font-data-table text-headline-lg text-tertiary" id="statCompleted">-</p>
    </div>
    <div class="clinical-card p-6">
        <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">PENDING PICKUP</p>
        <p class="font-data-table text-headline-lg text-error" id="statPending">-</p>
    </div>
</section>

<div class="clinical-card overflow-hidden">
    <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between">
        <h3 class="font-headline-sm text-headline-sm">Order Pipeline</h3>
        <div class="flex gap-2">
            <select id="filterStatus" onchange="loadOrders()" class="h-9 px-3 rounded-lg border border-surface-container-highest text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-surface-container-lowest">
                <option value="">All Status</option>
                <option value="dicuci">Dicuci</option>
                <option value="dijemur">Dijemur</option>
                <option value="dilipat">Dilipat</option>
                <option value="dikemas">Dikemas</option>
                <option value="selesai">Selesai</option>
            </select>
            <input type="date" id="filterDate" onchange="loadOrders()" class="h-9 px-3 rounded-lg border border-surface-container-highest text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-surface-container-lowest">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Order ID</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Santri</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Weight</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Amount</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-center">Status</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-center">Payment</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Action</th>
                </tr>
            </thead>
            <tbody id="ordersList" class="divide-y divide-surface-container-highest"></tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-surface-container-low border-t border-surface-container-highest flex items-center justify-between">
        <span class="font-label-sm text-label-sm text-on-surface-variant" id="showingInfo">Showing 0 orders</span>
        <div class="flex gap-2" id="pagination"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;

function updateStats(orders) {
    const active = orders.filter(o => o.status !== 'selesai').length;
    const totalWeight = orders.reduce((sum, o) => sum + parseFloat(o.weight_kg), 0);
    const completedToday = orders.filter(o => o.status === 'selesai').length;
    const pendingPickup = orders.filter(o => o.status === 'selesai' && o.payment_status === 'belum_bayar').length;

    document.getElementById('statActive').textContent = active;
    document.getElementById('statWeight').innerHTML = totalWeight.toFixed(1) + ' <span class="text-body-md font-body-md">kg</span>';
    document.getElementById('statCompleted').textContent = completedToday;
    document.getElementById('statPending').textContent = pendingPickup;
}

async function loadOrders(page = 1) {
    currentPage = page;
    const status = document.getElementById('filterStatus').value;
    const date = document.getElementById('filterDate').value;
    let url = `/api/orders?page=${page}`;
    if (status) url += `&status=${status}`;
    if (date) url += `&date=${date}`;

    const res = await api(url);
    const data = res.data;

    document.getElementById('ordersList').innerHTML = data.orders.map(o => `
        <tr class="hover:bg-surface-container-lowest transition-colors group">
            <td class="px-6 py-4 font-data-mono text-data-mono text-primary">#${o.id}</td>
            <td class="px-6 py-4 font-body-md font-medium text-on-surface">${o.santri_name}</td>
            <td class="px-6 py-4 font-data-mono text-data-mono text-right">${o.weight_kg} kg</td>
            <td class="px-6 py-4 font-data-mono text-data-mono text-right">${formatPrice(o.total_price)}</td>
            <td class="px-6 py-4 text-center">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-primary-container text-on-primary-container">${o.status}</span>
            </td>
            <td class="px-6 py-4 text-center">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider ${o.payment_status === 'lunas' ? 'bg-primary-fixed-dim text-on-primary-fixed-variant' : 'bg-secondary-container text-on-secondary-container'}">${o.payment_status}</span>
            </td>
            <td class="px-6 py-4 text-right">
                <a href="/orders/${o.id}" class="text-primary font-label-sm text-label-sm hover:underline opacity-0 group-hover:opacity-100 transition-opacity">Detail</a>
            </td>
        </tr>
    `).join('');

    updateStats(data.orders);

    const p = data.pagination;
    document.getElementById('showingInfo').textContent = `Showing ${data.orders.length} of ${p.total} orders`;

    let pagesHtml = '';
    for (let i = 1; i <= p.last_page; i++) {
        pagesHtml += `<button onclick="loadOrders(${i})" class="w-8 h-8 flex items-center justify-center rounded font-label-sm text-xs ${i === currentPage ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high'}">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = pagesHtml;
}

document.getElementById('filterDate').value = new Date().toISOString().split('T')[0];
loadOrders();
</script>
@endsection
