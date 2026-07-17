@extends('components.layout', ['activeNav' => 'santri'])

@section('title', 'Santri - Laundry Al-Insyiroh')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Santri Directory</h2>
        <p class="text-on-surface-variant font-body-md">Manage laundry profiles for students of Pesantren Al-Insyiroh.</p>
    </div>
    <button onclick="showAddModal()" class="bg-primary hover:opacity-90 text-on-primary font-label-sm text-label-sm py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        Add Santri
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
    <div class="md:col-span-8">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input type="text" id="searchInput" onkeyup="loadSantri()" placeholder="Search by name..." class="w-full pl-12 pr-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="santriGrid"></div>

<div id="addModal" class="fixed inset-0 bg-black/30 hidden items-center justify-center z-50">
    <div class="bg-surface-container-lowest rounded-xl p-6 w-full max-w-md mx-4 border border-surface-container-highest">
        <h2 class="font-headline-sm text-headline-sm mb-4">Tambah Santri</h2>
        <form onsubmit="return addSantri(event)">
            <div class="mb-3">
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Nama</label>
                <input type="text" name="name" required
                    class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <div class="mb-3">
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Kamar</label>
                <input type="text" name="kamar"
                    class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <div class="mb-4">
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Devisi</label>
                <input type="text" name="devisi"
                    class="w-full h-10 px-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="hideAddModal()" class="px-4 py-2 border border-outline-variant rounded-lg font-label-sm text-label-sm text-on-surface-variant">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadSantri() {
    const search = document.getElementById('searchInput').value;
    const res = await api('/api/santri?search=' + encodeURIComponent(search));
    document.getElementById('santriGrid').innerHTML = res.data.map(s => `
        <div class="clinical-card p-6 rounded-xl cursor-pointer">
            <div class="flex items-start justify-between mb-md">
                <div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-3xl">account_circle</span>
                </div>
                <span class="px-3 py-1 rounded-full bg-primary-fixed-dim/20 text-on-primary-fixed-variant font-label-sm text-label-sm">Active</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface mb-xs">${s.name}</h3>
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">home</span>
                    <span class="font-body-md">${s.kamar || '-'}</span>
                </div>
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    <span class="font-body-md">${s.devisi || '-'}</span>
                </div>
            </div>
            <div class="mt-lg pt-md border-t border-surface-container-highest flex justify-between items-center">
                <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Weight</span>
                <span class="font-data-mono text-headline-sm text-primary">${s.total_weight} kg</span>
            </div>
        </div>
    `).join('');
}

function showAddModal() { document.getElementById('addModal').classList.remove('hidden'); document.getElementById('addModal').classList.add('flex'); }
function hideAddModal() { document.getElementById('addModal').classList.add('hidden'); document.getElementById('addModal').classList.remove('flex'); }

async function addSantri(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    await api('/api/santri', {
        method: 'POST',
        body: JSON.stringify(Object.fromEntries(fd))
    });
    hideAddModal();
    e.target.reset();
    loadSantri();
}

loadSantri();
</script>
@endsection
