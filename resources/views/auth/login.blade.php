<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Laundry Al-Insyiroh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-surface text-on-surface antialiased font-body-md min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-container rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-primary text-3xl">local_laundry_service</span>
            </div>
            <h1 class="font-headline-md text-headline-md text-on-surface">Laundry Al-Insyiroh</h1>
            <p class="text-on-surface-variant font-body-md mt-1">Pesantren Al-Insyiroh</p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-surface-container-highest p-6">
            <form id="loginForm" onsubmit="return handleLogin(event)">
                <div class="mb-4">
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">email</span>
                        <input type="email" name="email" required
                            class="w-full h-10 pl-10 pr-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">lock</span>
                        <input type="password" name="password" required
                            class="w-full h-10 pl-10 pr-3 rounded-lg border border-outline-variant text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>
                </div>
                <p id="loginError" class="text-sm text-error mb-3 hidden"></p>
                <button type="submit" id="loginBtn"
                    class="w-full h-10 bg-primary text-on-primary rounded-lg font-label-sm text-label-sm hover:opacity-90 transition-all">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <script>
    async function handleLogin(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('loginBtn');
        const errEl = document.getElementById('loginError');
        errEl.classList.add('hidden');
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        try {
            const res = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: form.email.value, password: form.password.value })
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            window.location.href = '/dashboard';
        } catch (err) {
            errEl.textContent = err.message || 'Login gagal';
            errEl.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = 'Masuk';
        }
    }
    </script>
</body>
</html>
