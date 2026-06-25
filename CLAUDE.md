# MSD Fleet Management System

## Stack
- Laravel 12 + Blade + Tailwind CSS
- MySQL (fleet_msd database)
- Laragon on Windows (PHP 8.3.30)

## Quick Start
```bash
# Set PATH (Laragon)
$env:PATH = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin;$env:PATH"

# Fresh install
php artisan migrate:fresh --seed
npm run build

# Dev server
php artisan serve
```

## Key Conventions
- UI language is Bahasa Malaysia (Malay)
- 4 RBAC roles: admin, fleet, staff, guard (via Spatie Permission)
- Navigation config in `config/fleet.php` — sidebar items vary by role
- Layout: `resources/views/components/fleet-layout.blade.php`
- CSS: `public/css/fleet.css` (not Tailwind — custom CSS matching prototype)
- All controllers are in `app/Http/Controllers/`
- Demo users: admin@msd.com.my, fleet@msd.com.my, staff@msd.com.my, guard@msd.com.my (password: "password")

## Database
- 14 tables, 8 main models
- Seeds: RoleSeeder → UserSeeder → VehicleSeeder → DemoDataSeeder

## Modules
Dashboard, Vehicles, Services, Road Tax & Insurance, Fuel, Movements, Saman (Fines), Approvals (3-tier), Reminders, Anomaly Detection, QR Codes, Reports (PDF), Users, Settings
