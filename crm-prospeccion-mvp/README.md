# CRM Prospección MVP

MVP interno para gestionar clientes contactados, demos pendientes, seguimientos, presupuestos y estadísticas semanales.

Stack recomendado:

- Laravel 12
- Filament 3.3
- MySQL/MariaDB
- FullCalendar por CDN
- PWA básica para instalar desde el móvil

## Instalación rápida

```bash
composer create-project laravel/laravel crm-prospeccion "12.*"
cd crm-prospeccion

composer require filament/filament:"^3.3" -W
php artisan filament:install --panels
php artisan make:filament-user
```

Configura `.env` con MySQL:

```env
APP_NAME="CRM Prospeccion"
APP_URL=http://crm-prospeccion.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_prospeccion
DB_USERNAME=root
DB_PASSWORD=
```

Copia los archivos de este paquete dentro del proyecto Laravel.

Luego ejecuta:

```bash
php artisan migrate
php artisan db:seed --class=LeadSeeder
php artisan serve
```

Entra en:

```text
http://127.0.0.1:8000/admin
```

## Producción

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

Para PWA necesitas HTTPS en el servidor.
