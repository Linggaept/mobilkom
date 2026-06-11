# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# First-time setup
composer setup

# Run dev server (Laravel + queue worker + Vite, concurrently)
composer dev

# Run all tests
composer test

# Run a single test
php artisan test --filter TestName

# Lint (Laravel Pint)
./vendor/bin/pint

# Migrations + seed
php artisan migrate --seed

# Generate PDF (uses barryvdh/laravel-dompdf)
# Triggered via Admin/Pimpinan laporan controllers, not artisan
```

## Architecture

**Domain**: Sistem laporan kerusakan radio (radio equipment damage reporting) for Pertamina Hulu Rokan sites.

### Roles

Four roles enforced by `RoleMiddleware` (alias `role`) registered in `bootstrap/app.php`:

| Role | Can do |
|------|--------|
| `pelapor` | Submit, edit (before verified), delete (before verified/rejected) laporan |
| `teknisi` | View assigned laporan, update status, add technician notes + signature |
| `admin` | Verify/reject laporan, assign teknisi, export PDF (single + bulk), manage users |
| `pimpinan` | Read-only view of all laporan, export PDF |

Inactive accounts (`is_active = false`) are logged out immediately in the middleware.

### Controller Namespacing

Controllers are namespaced by role under `app/Http/Controllers/{Role}/`. Each role has its own `DashboardController` and `LaporanController`. `AuthController` and `NotificationController` are top-level.

### Laporan Lifecycle

Status enum: `menunggu_verifikasi` → `diverifikasi` → `sedang_proses` → `selesai` / `ditolak`

- Incident number format: `INC000000000001` (generated in `Laporan::generateNomor()` with `lockForUpdate`)
- Deadlines: 24h for admin verification, 48h after verification for technician completion
- Deadline tracking is computed via accessors on `Laporan` model (no stored deadline columns)
- Digital signatures stored as base64 in `ttd_pelapor` / `ttd_teknisi`
- `Laporan` uses soft deletes

### Authorization

`LaporanPolicy` handles `view` / `update` / `delete` gates. Pelapor can only update/delete when status is `menunggu_verifikasi` (or `ditolak` for delete).

### Notifications

Database-only (no mail). Three notification classes in `app/Notifications/`:
- `LaporanMasukNotification` — fired when new laporan submitted (notifies admin)
- `StatusLaporanNotification` — fired on status change (notifies pelapor)
- `TugasTeknisiNotification` — fired when teknisi assigned (notifies teknisi)

Notification data includes a `url` key pointing to the role-appropriate show route.

### Views

Blade templates mirroring controller namespace: `resources/views/{role}/laporan/`. PDF templates in `resources/views/pdf/` (single, single_partial, bulk). Shared layout at `resources/views/layouts/app.blade.php`.

### Infrastructure

- DB: MySQL (`mobilkom` database)
- Queue, session, cache: all on database driver
- File storage: local disk (`storage/app`)
- Queue must be running for notifications (`php artisan queue:listen` — included in `composer dev`)

### Seed Credentials

| Role | Email | Password |
|------|-------|----------|
| admin | admin@mobilkom.com | admin123 |
| pimpinan | pimpinan@mobilkom.com | pimpinan123 |
| teknisi | teknisi1@mobilkom.com | teknisi123 |
| pelapor | pelapor@mobilkom.com | pelapor123 |
