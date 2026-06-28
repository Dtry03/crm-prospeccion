# Instalación paso a paso

## 1. Crear proyecto

```bash
composer create-project laravel/laravel crm-prospeccion "12.*"
cd crm-prospeccion
```

## 2. Instalar Filament

```bash
composer require filament/filament:"^3.3" -W
php artisan filament:install --panels
php artisan make:filament-user
```

## 3. Configurar base de datos

Crea una base de datos:

```sql
CREATE DATABASE crm_prospeccion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edita `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_prospeccion
DB_USERNAME=root
DB_PASSWORD=
```

## 4. Copiar archivos

Copia este paquete sobre la raíz de tu proyecto Laravel.

## 5. Migrar

```bash
php artisan migrate
php artisan db:seed --class=LeadSeeder
```

## 6. Entrar al panel

```bash
php artisan serve
```

Abre:

```text
http://127.0.0.1:8000/admin
```
