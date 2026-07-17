<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@yield('title', 'Laundry Al-Insyiroh')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-surface text-on-surface antialiased font-body-md min-h-screen overflow-x-hidden">

<div id="sidebarOverlay" class="fixed inset-0 bg-black/20 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

<header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-4 md:px-6 h-16 bg-surface border-b border-surface-container-highest">
    <div class="flex items-center gap-3">
        <button class="material-symbols-outlined text-primary md:hidden" onclick="toggleSidebar()">menu</button>
        <h1 class="font-headline-sm text-headline-sm font-semibold text-on-surface truncate">Laundry Al-Insyiroh</h1>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <span class="hidden md:block text-sm text-on-surface-variant" id="headerUserName">Admin</span>
        <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-bold text-xs shrink-0 overflow-hidden border border-outline-variant">
            <span id="headerAvatar">A</span>
        </div>
    </div>
</header>

<aside id="sidebar" class="fixed top-16 left-0 bottom-0 w-64 bg-surface border-r border-surface-container-highest z-40 flex-col py-6 px-3 -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out md:flex">
    <nav class="space-y-0.5 flex-1">
        <a href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all @if(($activeNav ?? '') === 'dashboard') bg-primary-container text-on-primary-container @else text-on-surface-variant hover:bg-surface-container-low @endif">
            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'dashboard') 1 @else 0 @endif">dashboard</span>
            <span class="text-sm font-medium">Dashboard</span>
        </a>
        <a href="/orders" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all @if(($activeNav ?? '') === 'orders') bg-primary-container text-on-primary-container @else text-on-surface-variant hover:bg-surface-container-low @endif">
            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'orders') 1 @else 0 @endif">local_laundry_service</span>
            <span class="text-sm font-medium">Orders</span>
        </a>
        <a href="/santri" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all @if(($activeNav ?? '') === 'santri') bg-primary-container text-on-primary-container @else text-on-surface-variant hover:bg-surface-container-low @endif">
            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'santri') 1 @else 0 @endif">person</span>
            <span class="text-sm font-medium">Santri</span>
        </a>
        <a href="/reports" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all @if(($activeNav ?? '') === 'reports') bg-primary-container text-on-primary-container @else text-on-surface-variant hover:bg-surface-container-low @endif">
            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'reports') 1 @else 0 @endif">analytics</span>
            <span class="text-sm font-medium">Reports</span>
        </a>
    </nav>
    <div class="mt-auto pt-4 border-t border-surface-container-highest">
        <button onclick="logout()" class="flex items-center gap-3 px-4 py-2.5 w-full rounded-xl text-on-surface-variant hover:bg-surface-container-low transition-colors text-sm">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="font-medium">Logout</span>
        </button>
    </div>
</aside>

<main class="pt-16 min-h-screen md:ml-64">
    <div class="px-4 md:px-6 pt-6 pb-24 md:pb-10 max-w-7xl mx-auto">
        @yield('content')
    </div>
</main>

<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center h-16 bg-surface border-t border-surface-container-highest safe-area-bottom">
    <a href="/dashboard" class="flex flex-col items-center justify-center w-full h-full @if(($activeNav ?? '') === 'dashboard') text-primary @else text-on-surface-variant @endif">
        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'dashboard') 1 @else 0 @endif">dashboard</span>
        <span class="text-[10px] leading-none mt-0.5 font-medium">Dashboard</span>
    </a>
    <a href="/orders" class="flex flex-col items-center justify-center w-full h-full @if(($activeNav ?? '') === 'orders') text-primary @else text-on-surface-variant @endif">
        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'orders') 1 @else 0 @endif">local_laundry_service</span>
        <span class="text-[10px] leading-none mt-0.5 font-medium">Orders</span>
    </a>
    <a href="/santri" class="flex flex-col items-center justify-center w-full h-full @if(($activeNav ?? '') === 'santri') text-primary @else text-on-surface-variant @endif">
        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'santri') 1 @else 0 @endif">person</span>
        <span class="text-[10px] leading-none mt-0.5 font-medium">Santri</span>
    </a>
    <a href="/reports" class="flex flex-col items-center justify-center w-full h-full @if(($activeNav ?? '') === 'reports') text-primary @else text-on-surface-variant @endif">
        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' @if(($activeNav ?? '') === 'reports') 1 @else 0 @endif">analytics</span>
        <span class="text-[10px] leading-none mt-0.5 font-medium">Reports</span>
    </a>
</nav>

<script>
    const token = localStorage.getItem('token');

    async function api(path, options = {}) {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (token) headers['Authorization'] = `Bearer ${token}`;
        const res = await fetch(path, { ...options, headers });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Request failed');
        return data;
    }

    function logout() {
        api('/api/logout', { method: 'POST' }).finally(() => {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        });
    }

    function formatPrice(n) {
        return 'Rp' + Number(n).toLocaleString('id-ID');
    }

    function getInitials(name) {
        if (!name) return 'A';
        return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.name) {
        const el = document.getElementById('headerUserName');
        if (el) el.textContent = user.name;
        const av = document.getElementById('headerAvatar');
        if (av) av.textContent = getInitials(user.name);
    }

    if (!localStorage.getItem('token') && !window.location.pathname.includes('/login')) {
        window.location.href = '/login';
    }
</script>
@yield('scripts')
</body>
</html>
