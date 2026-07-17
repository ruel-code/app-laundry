@extends('components.layout', ['activeNav' => 'orders'])

@section('title', 'Order Detail - Laundry Al-Insyiroh')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">Order #<span id="orderId" class="text-primary"></span></h2>
        <p class="text-on-surface-variant font-body-md">Order details and management.</p>
    </div>
    <a href="/orders" class="text-primary font-label-sm text-label-sm hover:underline flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
    </a>
</div>

<div class="clinical-card p-6">
    <div class="grid grid-cols-2 gap-6 mb-6" id="orderInfo"></div>

    <div class="mb-6">
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-4">Order Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low">
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant">Item</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant text-center">Qty</th>
                        <th class="px-4 py-3 font-label-sm text-label-sm text-on-surface-variant text-right">Weight</th>
                    </tr>
                </thead>
                <tbody id="orderItems" class="divide-y divide-surface-container-highest"></tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <button id="btnStatus" onclick="updateStatus()" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm hover:opacity-90"></button>
        <button id="btnPayment" onclick="updatePayment()" class="px-4 py-2 border border-surface-container-highest rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container-low"></button>
        <button onclick="window.open('/api/orders/' + getOrderId() + '/nota', '_blank')" class="px-4 py-2 border border-surface-container-highest rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container-low flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">print</span> Print Nota
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
let orderData = null;
function getOrderId() { return window.location.pathname.split('/').pop(); }

async function loadOrder() {
    try {
        const res = await api('/api/orders/' + getOrderId());
        orderData = res.data;
        const o = orderData;

        document.getElementById('orderId').textContent = o.id;
        document.getElementById('orderInfo').innerHTML = `
            <div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Santri</span>
                <p class="font-body-md font-medium mt-1">${o.santri.name}</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Cashier</span>
                <p class="font-body-md font-medium mt-1">${o.user.name}</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Weight</span>
                <p class="font-data-mono font-medium mt-1">${o.weight_kg} kg (rounded: ${o.rounded_weight} kg)</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Total</span>
                <p class="font-data-mono font-headline-sm text-primary mt-1">${formatPrice(o.total_price)}</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Status</span>
                <p class="mt-1"><span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-primary-container text-on-primary-container">${o.status}</span></p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Payment</span>
                <p class="mt-1"><span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider ${o.payment_status === 'lunas' ? 'bg-primary-fixed-dim text-on-primary-fixed-variant' : 'bg-secondary-container text-on-secondary-container'}">${o.payment_status}</span></p>
            </div>
            ${o.discount_kg > 0 ? `<div class="bg-surface-container-low p-4 rounded-lg">
                <span class="text-label-sm font-label-sm text-on-surface-variant">Discount</span>
                <p class="font-data-mono font-medium mt-1 text-primary">${o.discount_kg} kg free</p>
            </div>` : ''}
        `;

        const itemsTbody = document.getElementById('orderItems');
        if (o.items && o.items.length > 0) {
            itemsTbody.innerHTML = o.items.map(item => `
                <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="px-4 py-3 font-body-md">${item.item_name}</td>
                    <td class="px-4 py-3 text-center font-data-mono">${item.quantity}</td>
                    <td class="px-4 py-3 text-right font-data-mono">${item.weight_kg} kg</td>
                </tr>
            `).join('');
        } else {
            itemsTbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-on-surface-variant">No items</td></tr>';
        }

        const statusOrder = ['dicuci', 'dijemur', 'dilipat', 'dikemas', 'selesai'];
        const currentIdx = statusOrder.indexOf(o.status);
        const btnStatus = document.getElementById('btnStatus');
        if (currentIdx < statusOrder.length - 1) {
            btnStatus.textContent = 'Advance to ' + statusOrder[currentIdx + 1];
            btnStatus.disabled = false;
        } else {
            btnStatus.textContent = 'Completed';
            btnStatus.disabled = true;
            btnStatus.className = 'px-4 py-2 bg-surface-container-high text-on-surface-variant rounded-lg font-label-sm text-label-sm cursor-not-allowed';
        }

        const btnPayment = document.getElementById('btnPayment');
        if (o.payment_status === 'belum_bayar') {
            btnPayment.textContent = 'Mark as Paid';
            btnPayment.disabled = false;
        } else {
            btnPayment.textContent = 'Paid';
            btnPayment.disabled = true;
            btnPayment.className = 'px-4 py-2 border border-surface-container-highest rounded-lg font-label-sm text-label-sm text-on-surface-variant cursor-not-allowed opacity-50';
        }
    } catch (e) { console.error(e); }
}

async function updateStatus() {
    if (!orderData) return;
    const statusOrder = ['dicuci', 'dijemur', 'dilipat', 'dikemas', 'selesai'];
    const currentIdx = statusOrder.indexOf(orderData.status);
    if (currentIdx >= statusOrder.length - 1) return;
    const nextStatus = statusOrder[currentIdx + 1];
    try {
        await api('/api/orders/' + orderData.id + '/status', { method: 'PATCH', body: JSON.stringify({ status: nextStatus }) });
        loadOrder();
    } catch (e) { alert('Error: ' + e.message); }
}

async function updatePayment() {
    if (!orderData || orderData.payment_status === 'lunas') return;
    try {
        await api('/api/orders/' + orderData.id + '/payment', { method: 'PATCH', body: JSON.stringify({ payment_status: 'lunas' }) });
        loadOrder();
    } catch (e) { alert('Error: ' + e.message); }
}

loadOrder();
</script>
@endsection
