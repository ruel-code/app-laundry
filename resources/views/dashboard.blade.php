@extends('components.layout', ['activeNav' => 'dashboard'])

@section('title', 'Dashboard - Laundry Al-Insyiroh')

@section('content')
<section class="mb-8">
    <span class="font-label-sm text-label-sm text-primary uppercase tracking-widest">Dashboard</span>
    <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Welcome back, <span id="userName">Admin</span></h2>
    <p class="text-on-surface-variant font-body-md mt-1">Here is what is happening with the laundry services today.</p>
</section>

<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="clinical-card p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex justify-between items-start">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Today's Orders</span>
            <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-sm">inventory</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg font-data-mono text-on-surface" id="statOrders">-</div>
        </div>
    </div>
    <div class="clinical-card p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex justify-between items-start">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Today's Revenue</span>
            <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center">
                <span class="material-symbols-outlined text-on-surface text-sm">payments</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg font-data-mono text-on-surface" id="statRevenue">-</div>
        </div>
    </div>
    <div class="clinical-card p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex justify-between items-start">
            <span class="font-label-sm text-label-sm text-on-surface-variant">New Santri</span>
            <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary text-sm">person_add</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg font-data-mono text-on-surface" id="statSantri">-</div>
        </div>
    </div>
    <div class="clinical-card p-6 flex flex-col justify-between min-h-[140px]">
        <div class="flex justify-between items-start">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Promo Used</span>
            <div class="w-8 h-8 rounded-full bg-primary-fixed-dim flex items-center justify-center">
                <span class="material-symbols-outlined text-on-primary-fixed-variant text-sm">redeem</span>
            </div>
        </div>
        <div>
            <div class="font-headline-lg text-headline-lg font-data-mono text-on-surface" id="statPromo">-</div>
        </div>
    </div>
</section>

<div class="flex gap-3 mb-6">
    <a href="/orders/create" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm hover:opacity-90 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">add</span> New Order
    </a>
    <a href="/orders" class="px-4 py-2 border border-surface-container-highest rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">View Orders</a>
    <a href="/reports" class="px-4 py-2 border border-surface-container-highest rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">Reports</a>
</div>

<div class="clinical-card overflow-hidden">
    <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between">
        <h3 class="font-headline-sm text-headline-sm text-on-surface">Recent Orders</h3>
        <a href="/orders" class="text-primary font-label-sm text-label-sm hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">#</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">Santri</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">Weight</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">Status</th>
                    <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant">Action</th>
                </tr>
            </thead>
            <tbody id="recentOrders" class="divide-y divide-surface-container-highest"></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadDashboard() {
    try {
        const res = await api('/api/dashboard');
        const d = res.data;
        document.getElementById('statOrders').textContent = d.total_orders_today;
        document.getElementById('statRevenue').textContent = formatPrice(d.total_revenue_today);
        document.getElementById('statSantri').textContent = d.new_santri_today;
        document.getElementById('statPromo').textContent = d.active_promo;

        const tbody = document.getElementById('recentOrders');
        tbody.innerHTML = d.recent_orders.map(o => `
            <tr class="hover:bg-surface-container-lowest transition-colors">
                <td class="px-6 py-4 font-data-mono text-data-mono text-primary">#${o.id}</td>
                <td class="px-6 py-4 font-body-md font-medium">${o.santri_name}</td>
                <td class="px-6 py-4 font-data-mono text-data-mono">${o.weight_kg} kg</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-primary-container text-on-primary-container">${o.status}</span>
                </td>
                <td class="px-6 py-4">
                    <a href="/orders/${o.id}" class="text-primary font-label-sm text-label-sm hover:underline">Detail</a>
                </td>
            </tr>
        `).join('');
    } catch (e) { console.error(e); }
}

if (user && user.name) document.getElementById('userName').textContent = user.name;

loadDashboard();
</script>
@endsection
