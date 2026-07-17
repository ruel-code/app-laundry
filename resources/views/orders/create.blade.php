@extends('components.layout', ['activeNav' => 'orders'])

@section('title', 'New Order - Laundry Al-Insyiroh')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface">New Order</h2>
        <p class="text-on-surface-variant font-body-md">Create a new laundry order for a santri.</p>
    </div>
    <a href="/orders" class="text-primary font-label-sm text-label-sm hover:underline flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
    </a>
</div>

<div class="clinical-card p-6 max-w-3xl">
    <form id="orderForm" onsubmit="return createOrder(event)">
        <div class="mb-4">
            <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Santri</label>
            <div class="flex gap-2">
                <input type="text" id="santriSearch" placeholder="Search santri name..." onkeyup="searchSantri()"
                    class="flex-1 h-10 px-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <a href="/santri" class="h-10 px-3 border border-outline-variant rounded-lg flex items-center font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container-low transition-colors">+ New</a>
            </div>
            <div id="santriResults" class="mt-1 hidden bg-surface-container-lowest border border-outline-variant rounded-lg overflow-hidden"></div>
            <input type="hidden" name="santri_id" id="santri_id">
        </div>

        <div id="santriInfo" class="hidden mb-4 p-4 bg-surface-container-low rounded-lg">
            <p class="font-body-md font-medium"><span id="santriName"></span> <span class="text-on-surface-variant">| Room: <span id="santriKamar"></span> | Div: <span id="santriDevisi"></span></span></p>
            <p class="text-sm text-on-surface-variant mt-1">Accumulated: <span id="santriWeight" class="font-data-mono"></span> kg</p>
        </div>

        <div class="mb-4">
            <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Weight (kg)</label>
            <input type="number" name="weight_kg" id="weightKg" step="0.1" min="1" required oninput="calculatePrice()"
                class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <p class="text-xs text-on-surface-variant mt-1">Rounded down. Minimum 1 kg.</p>
        </div>

        <div class="mb-4">
            <label class="block font-label-sm text-label-sm text-on-surface-variant mb-2">Items</label>
            <div id="itemsContainer">
                <div class="flex gap-2 mb-2 items-start">
                    <select name="item_name[]" class="flex-1 h-10 px-3 rounded-lg border border-outline-variant text-sm">
                        <option value="Baju">Baju</option>
                        <option value="Celana">Celana</option>
                        <option value="Sarung">Sarung</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <input type="number" name="quantity[]" placeholder="Qty" min="1" value="1"
                        class="w-20 h-10 px-3 rounded-lg border border-outline-variant text-sm text-center">
                    <button type="button" onclick="this.parentElement.remove()" class="h-10 px-2 text-on-surface-variant hover:text-error">&times;</button>
                </div>
            </div>
            <button type="button" onclick="addItem()" class="text-primary font-label-sm text-label-sm hover:underline mt-2">+ Add item</button>
        </div>

        <div id="promoInfo" class="hidden mb-4 p-3 bg-primary-fixed-dim/10 border border-primary-fixed-dim rounded-lg font-body-md text-on-primary-fixed-variant"></div>

        <div class="mb-6 p-4 bg-surface-container-low rounded-lg">
            <div class="flex justify-between text-sm mb-1">
                <span>Total: <span id="totalWeight">0</span> kg x Rp3,000</span>
                <span class="font-data-mono" id="totalBefore">Rp0</span>
            </div>
            <div class="flex justify-between text-sm mb-1">
                <span>Discount: <span id="discountKg">0</span> kg</span>
                <span class="font-data-mono text-primary" id="discountAmount">-Rp0</span>
            </div>
            <div class="flex justify-between font-semibold border-t border-surface-container-highest pt-2 mt-2">
                <span>Total Due</span>
                <span class="font-data-mono text-headline-sm text-primary" id="totalPay">Rp0</span>
            </div>
        </div>

        <div class="mb-4">
            <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Payment Status</label>
            <select name="payment_status" class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="belum_bayar">Unpaid</option>
                <option value="lunas">Paid</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 h-10 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm hover:opacity-90">Save</button>
            <button type="button" onclick="saveAndPrint()" class="flex-1 h-10 border border-primary text-primary rounded-lg font-label-sm text-label-sm hover:bg-primary/5">Save & Print Nota</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let selectedSantri = null;
let santriData = [];

async function searchSantri() {
    const q = document.getElementById('santriSearch').value.trim();
    const results = document.getElementById('santriResults');
    if (q.length < 2) { results.classList.add('hidden'); return; }
    const res = await api('/api/santri?search=' + encodeURIComponent(q));
    santriData = res.data;
    if (santriData.length === 0) { results.classList.add('hidden'); return; }
    results.innerHTML = santriData.map(s => `
        <div onclick="selectSantri(${s.id})" class="px-3 py-2 hover:bg-surface-container-low cursor-pointer border-b border-surface-container-highest text-sm">${s.name} - ${s.kamar || '-'}</div>
    `).join('');
    results.classList.remove('hidden');
}

function selectSantri(id) {
    const s = santriData.find(x => x.id === id);
    if (!s) return;
    selectedSantri = s;
    document.getElementById('santri_id').value = s.id;
    document.getElementById('santriSearch').value = s.name;
    document.getElementById('santriName').textContent = s.name;
    document.getElementById('santriKamar').textContent = s.kamar || '-';
    document.getElementById('santriDevisi').textContent = s.devisi || '-';
    document.getElementById('santriWeight').textContent = s.total_weight;
    document.getElementById('santriInfo').classList.remove('hidden');
    document.getElementById('santriResults').classList.add('hidden');
    calculatePrice();
}

function addItem() {
    const container = document.getElementById('itemsContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2 mb-2 items-start';
    div.innerHTML = `
        <select name="item_name[]" class="flex-1 h-10 px-3 rounded-lg border border-outline-variant text-sm">
            <option value="Baju">Baju</option>
            <option value="Celana">Celana</option>
            <option value="Sarung">Sarung</option>
            <option value="Lainnya">Lainnya</option>
        </select>
        <input type="number" name="quantity[]" placeholder="Qty" min="1" value="1"
            class="w-20 h-10 px-3 rounded-lg border border-outline-variant text-sm text-center">
        <button type="button" onclick="this.parentElement.remove()" class="h-10 px-2 text-on-surface-variant hover:text-error">&times;</button>
    `;
    container.appendChild(div);
}

function calculatePrice() {
    const weight = parseFloat(document.getElementById('weightKg').value) || 0;
    const rounded = Math.floor(weight);
    const pricePerKg = 3000;
    let discountKg = 0;
    if (selectedSantri) {
        const total = parseFloat(selectedSantri.total_weight) + rounded;
        if (total >= 10) discountKg = 1;
    }
    const chargeable = Math.max(0, rounded - discountKg);
    const totalPrice = chargeable * pricePerKg;

    document.getElementById('totalWeight').textContent = rounded;
    document.getElementById('totalBefore').textContent = formatPrice(rounded * pricePerKg);
    document.getElementById('discountKg').textContent = discountKg;
    document.getElementById('discountAmount').textContent = discountKg > 0 ? '-' + formatPrice(discountKg * pricePerKg) : 'Rp0';
    document.getElementById('totalPay').textContent = formatPrice(totalPrice);

    const promoEl = document.getElementById('promoInfo');
    if (discountKg > 0) {
        promoEl.innerHTML = 'PROMO: Accumulation reached 10kg. 1kg free!';
        promoEl.classList.remove('hidden');
        promoEl.className = 'mb-4 p-3 bg-primary-fixed-dim/10 border border-primary-fixed-dim rounded-lg font-body-md text-on-primary-fixed-variant';
    } else if (selectedSantri) {
        const remaining = 10 - (parseFloat(selectedSantri.total_weight) + rounded);
        if (remaining > 0) {
            promoEl.innerHTML = 'Promo 10kg: need ' + remaining.toFixed(1) + ' kg more for 1kg free.';
            promoEl.classList.remove('hidden');
            promoEl.className = 'mb-4 p-3 bg-secondary-container/20 border border-secondary-container rounded-lg font-body-md text-on-secondary-container';
        } else { promoEl.classList.add('hidden'); }
    } else { promoEl.classList.add('hidden'); }
}

async function createOrder(e) {
    e.preventDefault();
    const form = e.target;
    const santriId = document.getElementById('santri_id').value;
    if (!santriId) { alert('Select a santri first'); return; }
    const items = [];
    const itemNames = form.querySelectorAll('select[name="item_name[]"]');
    const quantities = form.querySelectorAll('input[name="quantity[]"]');
    itemNames.forEach((sel, i) => { items.push({ item_name: sel.value, quantity: parseInt(quantities[i].value) || 1, weight_kg: 0 }); });
    const body = { santri_id: parseInt(santriId), weight_kg: parseFloat(form.weight_kg.value), items: items.length > 0 ? items : undefined, payment_status: form.payment_status.value };
    try {
        const res = await api('/api/orders', { method: 'POST', body: JSON.stringify(body) });
        if (confirm('Order created! Print nota?')) { window.open('/api/orders/' + res.data.id + '/nota', '_blank'); }
        window.location.href = '/orders';
    } catch (e) { alert('Error: ' + e.message); }
}

async function saveAndPrint() {
    const form = document.getElementById('orderForm');
    const santriId = document.getElementById('santri_id').value;
    if (!santriId) { alert('Select a santri first'); return; }
    const items = [];
    const itemNames = form.querySelectorAll('select[name="item_name[]"]');
    const quantities = form.querySelectorAll('input[name="quantity[]"]');
    itemNames.forEach((sel, i) => { items.push({ item_name: sel.value, quantity: parseInt(quantities[i].value) || 1, weight_kg: 0 }); });
    const body = { santri_id: parseInt(santriId), weight_kg: parseFloat(form.weight_kg.value), items: items.length > 0 ? items : undefined, payment_status: form.payment_status.value };
    try {
        const res = await api('/api/orders', { method: 'POST', body: JSON.stringify(body) });
        window.open('/api/orders/' + res.data.id + '/nota', '_blank');
        window.location.href = '/orders';
    } catch (e) { alert('Error: ' + e.message); }
}
</script>
@endsection
