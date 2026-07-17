@extends('components.layout', ['activeNav' => 'reports'])

@section('title', 'Reports - Laundry Al-Insyiroh')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">FINANCIAL ANALYTICS</span>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mt-1">Financial Reports</h2>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex gap-2">
            <select id="reportType" onchange="toggleDateInputs()" class="h-10 px-3 rounded-lg border border-surface-container-highest text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-surface-container-lowest">
                <option value="daily">Daily</option>
                <option value="monthly">Monthly</option>
            </select>
            <div id="dailyInputs">
                <input type="date" id="reportDate" class="h-10 px-3 rounded-lg border border-surface-container-highest text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-surface-container-lowest">
            </div>
            <div id="monthlyInputs" class="hidden flex gap-2">
                <select id="reportMonth" class="h-10 px-3 rounded-lg border border-surface-container-highest text-sm bg-surface-container-lowest">
                    <option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option>
                    <option value="4">Apr</option><option value="5">May</option><option value="6">Jun</option>
                    <option value="7">Jul</option><option value="8">Aug</option><option value="9">Sep</option>
                    <option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
                </select>
                <input type="number" id="reportYear" value="2026" class="w-20 h-10 px-3 rounded-lg border border-surface-container-highest text-sm bg-surface-container-lowest">
            </div>
        </div>
        <button onclick="loadReport()" class="bg-primary text-on-primary font-label-sm text-label-sm px-4 py-2.5 rounded-lg flex items-center gap-2 hover:opacity-90 transition-all">
            <span class="material-symbols-outlined text-[18px]">refresh</span>
            Load
        </button>
    </div>
</div>

<div id="reportResult" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="summaryCards"></div>

    <div class="clinical-card overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between">
            <h3 class="font-headline-sm text-headline-sm">Transaction Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low">
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">#</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Santri</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Weight</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Amount</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase">Status</th>
                        <th class="px-6 py-4 font-label-sm text-label-sm text-on-surface-variant uppercase text-right">Discount</th>
                    </tr>
                </thead>
                <tbody id="reportOrders" class="divide-y divide-surface-container-highest"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleDateInputs() {
    const type = document.getElementById('reportType').value;
    document.getElementById('dailyInputs').classList.toggle('hidden', type !== 'daily');
    document.getElementById('monthlyInputs').classList.toggle('hidden', type !== 'monthly');
}

async function loadReport() {
    const type = document.getElementById('reportType').value;
    let url;
    if (type === 'daily') {
        const date = document.getElementById('reportDate').value || new Date().toISOString().split('T')[0];
        url = `/api/reports/daily?date=${date}`;
    } else {
        const month = document.getElementById('reportMonth').value;
        const year = document.getElementById('reportYear').value;
        url = `/api/reports/monthly?month=${month}&year=${year}`;
    }
    try {
        const res = await api(url);
        const d = res.data;
        const s = d.summary;

        document.getElementById('reportResult').classList.remove('hidden');

        document.getElementById('summaryCards').innerHTML = `
            <div class="clinical-card p-6">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">TOTAL ORDERS</p>
                <p class="font-data-table text-headline-lg text-primary">${s.total_orders}</p>
            </div>
            <div class="clinical-card p-6">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">TOTAL WEIGHT</p>
                <p class="font-data-table text-headline-lg text-secondary">${s.total_weight}<span class="text-body-md font-body-md ml-1">kg</span></p>
            </div>
            <div class="clinical-card p-6">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">TOTAL REVENUE</p>
                <p class="font-data-table text-headline-lg text-primary">${formatPrice(s.total_revenue)}</p>
            </div>
            <div class="clinical-card p-6">
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-1">DISCOUNT</p>
                <p class="font-data-table text-headline-lg text-tertiary">${s.total_discount_kg}<span class="text-body-md font-body-md ml-1">kg</span></p>
            </div>
        `;

        document.getElementById('reportOrders').innerHTML = d.orders.map(o => `
            <tr class="hover:bg-surface-container-lowest transition-colors">
                <td class="px-6 py-4 font-data-mono text-data-mono text-primary">${o.id}</td>
                <td class="px-6 py-4 font-body-md font-medium">${o.santri_name}</td>
                <td class="px-6 py-4 font-data-mono text-data-mono text-right">${o.weight_kg} kg</td>
                <td class="px-6 py-4 font-data-mono text-data-mono text-right">${formatPrice(o.total_price)}</td>
                <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-primary-container text-on-primary-container">${o.status}</span></td>
                <td class="px-6 py-4 font-data-mono text-data-mono text-right">${parseFloat(o.discount_kg) > 0 ? o.discount_kg + ' kg' : '-'}</td>
            </tr>
        `).join('');
    } catch (e) { alert('Error: ' + e.message); }
}

document.getElementById('reportDate').value = new Date().toISOString().split('T')[0];
</script>
@endsection
