# app-Laundry

Laundry management app for Pesantren Al-Insyiroh. Laravel 13 + SQLite + Blade (SPA-like with inline JS) + Sanctum API.

## Dev commands

```bash
composer setup       # full install: composer install, .env, key:generate, migrate, npm install/build
composer dev         # run all: php artisan serve + queue:listen + pail + vite (via concurrently)
composer test        # config:clear then php artisan test
npm run build        # vite build
npm run dev          # vite dev server
```

## Architecture

- **Auth**: Sanctum token API. Login via `POST /api/login` returns token; stored in localStorage. All API routes under `auth:sanctum` middleware.
- **Web routes** (`routes/web.php`): Serve Blade views only — no controllers. Auth gate is client-side (localStorage token check).
- **API routes** (`routes/api.php`): All JSON endpoints. Response envelope: `{success, data, message}`.
- **Pricing**: Rp 3,000/kg. `weight_kg` is floor-rounded for billing (e.g. 2.7 kg → 2 kg chargeable).
- **Loyalty**: Every 10 kg accumulated → 1 kg free on next order. Tracked via `santri.total_weight` and `loyalty_logs`.
- **Order status flow** (strictly sequential): `dicuci → dijemur → dilipat → dikemas → selesai`. Can only advance 1 step at a time. Enforced in `OrderController::update`.
- **Pages**: login, dashboard, santri, orders (index/create/show), reports.

## Key data model

- `Santri` — name, kamar (room), devisi (division), total_weight (lifetime accumulation)
- `Order` — santri_id, user_id, weight_kg, total_price, item_details (JSON), status, payment_status, discount_kg
- `OrderItem` — order_id, item_name, quantity, weight_kg
- `LoyaltyLog` — santri_id, order_id, free_kg, total_accumulated

## DB / env

- Default: SQLite (`database/database.sqlite`). All queue/cache/session backed by `database` driver.
- `.env.example` has clean defaults. Copy and `php artisan key:generate` on fresh install.

## PDF nota

- Thermal-printer-sized PDF (`laravel-dompdf`) via `GET /api/orders/{order}/nota` → downloads `nota-{id}.pdf`.
- Uses `Barryvdh\DomPDF\Facade\Pdf`. Paper: `[0, 0, 226.77, 800]` (~80mm thermal roll).
- View: `resources/views/pdf/nota.blade.php`.

## Frontend

- **Tailwind CSS v4** with `@tailwindcss/vite` plugin. Custom font theme: Inter (sans), JetBrains Mono (mono).
- App theme color: `#0D9488` (teal-600).
- No JS framework — vanilla `fetch()` calls, token from `localStorage.getItem('token')`, user display name from `localStorage.getItem('user')`.
- Vue/React/Alpine are NOT used.

## Testing

- PHPUnit 12. `tests/Unit/`, `tests/Feature/`. DB defaults to `:memory:` SQLite in phpunit.xml.
- Factory: `UserFactory` available.
- Run single test: `php artisan test --filter=MethodName`.

## Code style

- Laravel Pint installed. Run: `./vendor/bin/pint`.
- PSR-4: `App\` in `app/`, `Tests\` in `tests/`.
- API response pattern: `return response()->json(['success' => true, 'data' => $data, 'message' => '...'])`.

## Gotchas

- `total_price` cast to `(int)` in all JSON responses — values truncated, not rounded.
- Order update PATCH routes (`/status`, `/payment`) both route to the same `OrderController::update` method.
- `weight_kg` is stored as decimal but `total_price` calculated on `floor(weight_kg)`. The fractional part is tracked (for accumulation) but not billed.
- 401 responses return `{success: false, data: null, message: 'Unauthorized'}` (not standard Laravel auth).
