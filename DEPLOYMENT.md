# PATELINTERIORS — Production Deployment Guide

This guide describes how to deploy the upgraded Laravel 12 / PHP 8.2 codebase for **Patel Interiors** with zero disruption, zero data modification, and instant rollback capability.

---

## 1. System Requirements

### PHP 8.2+
- **PHP Version:** `PHP 8.2.12` or higher (tested on 8.2.12).
- **Required Extensions:**
  - `pdo_mysql`
  - `openssl`
  - `mbstring`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `fileinfo`
  - `gd`
  - `zip`
  - `bcmath`
  - `curl`
- **Recommended `php.ini` Directives:**
  ```ini
  memory_limit = 512M
  max_execution_time = 300
  upload_max_filesize = 50M
  post_max_size = 50M
  ```

### Database Server
- **Server:** MariaDB 10.4+ or MySQL 8.0+.
- **Database Schema:** Single source of truth is `patel.sql`. **Do NOT run migrations or seeders.** The database schema remains 100% untouched.

---

## 2. Pre-Deployment Preparation

1. **Full Database Backup:**
   ```bash
   mysqldump -u <db_user> -p patel > patel_backup_$(date +%Y%m%d_%H%M%S).sql
   ```
2. **Current Codebase Backup:**
   Create an archive or keep the current production directory intact for zero-risk rollback:
   ```bash
   # Example: keep /var/www/patelinteriors-old58 alongside /var/www/patelinteriors-l12
   ```

---

## 3. Deployment Steps

### Step 1: Copy Codebase to Target Server
Deploy the upgraded files from `PATELINTERIORS-laravel12` to the production location (e.g. `/var/www/patelinteriors` or `d:\xampp\htdocs\patelinteriors.co.in`).

### Step 2: Environment Configuration (`.env`)
Copy your existing production `.env` to the new deployment root.
Ensure the following settings:
- **`APP_KEY`:** Must remain **identical** to the current production key.
  > ⚠️ **CRITICAL:** NEVER run `php artisan key:generate`. Running it will invalidate all existing password hashes and encrypted payloads.
- **`JWT_SECRET`:** Must remain **identical** to the existing production secret to allow existing mobile app logins to continue uninterrupted.
- **`MAIL_MAILER` / `MAIL_DRIVER`:**
  In Laravel 12, Symfony Mailer is standard. Both `MAIL_MAILER` and `MAIL_DRIVER` are supported:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your_email@domain.com
  MAIL_PASSWORD=your_app_password
  MAIL_ENCRYPTION=tls
  ```

### Step 3: Install Production Dependencies
Run composer without development packages:
```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### Step 4: Storage & File Permissions
Ensure the web server user (`www-data` on Linux, `SYSTEM`/`IUSR` on Windows IIS/XAMPP) has read/write permissions on:
- `storage/`
- `storage/logs/`
- `storage/framework/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `bootstrap/cache/`
- `public/uploads/` (if any user uploads are written here)

### Step 5: Optimization & Caching Commands
Run the following optimization commands:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

> ⚠️ **IMPORTANT — DO NOT RUN `route:cache`:**
> Pre-existing route definitions in `routes/web.php` include duplicate route names across different controller modules (e.g., duplicate `login`, `lead.edit`, `index`). Running `php artisan route:cache` will intentionally throw a `LogicException`. Keep routes dynamically resolved via `RouteServiceProvider`, which has custom duplicate resolution preserving Laravel 5.8 behavior.

> ⚠️ **DO NOT RUN `php artisan migrate`:**
> Migrations are unmaintained in this project. The database schema in production is already fully compatible.

### Step 6: Web Server Configuration (VHost)
Ensure your web server document root points to the `public/` directory:
- **Apache:** `DocumentRoot "path/to/PATELINTERIORS-laravel12/public"`
  - Verify `mod_rewrite` is enabled.
- **Nginx:**
  ```nginx
  server {
      listen 80;
      server_name patelinteriors.co.in;
      root /path/to/PATELINTERIORS-laravel12/public;
      index index.php index.html;

      location / {
          try_files $uri $uri/ /index.php?$query_string;
      }

      location ~ \.php$ {
          fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
          fastcgi_index index.php;
          include fastcgi_params;
          fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
      }
  }
  ```

### Step 7: Background Cron Setup
If scheduled cron commands are configured on the production server, point them to the PHP 8.2 binary:
```cron
# Every minute run Laravel scheduler
* * * * * php8.2 /path/to/PATELINTERIORS-laravel12/artisan schedule:run >> /dev/null 2>&1
```

---

## 4. Post-Deployment Verification

1. **Public Login Screen:**
   Open `https://patelinteriors.co.in/` -> Verify the Metronic sign-in screen loads with stylesheet and assets.
2. **Admin Authentication:**
   Sign in as an administrator -> Verify dashboard metrics and navigation sidebar load cleanly.
3. **DataTables Modules:**
   Open `/user/lead` and `/user/client` -> Verify records paginate and search without AJAX errors.
4. **Mobile API:**
   Verify mobile app field staff can check in attendance and retrieve user profile details (`/api/v1/get-user`).
5. **Log Inspection:**
   Monitor `storage/logs/laravel-YYYY-MM-DD.log` for any runtime notices.

---

## 5. Instant Rollback Plan

If unexpected behavior occurs in production, revert within 60 seconds:

1. **Re-point Document Root:**
   Change the Apache `DocumentRoot` or Nginx `root` back to the previous directory (e.g. `patelinteriors-old58/public`).
2. **Switch PHP Version (if multi-PHP):**
   Switch PHP-FPM or Apache handler back to PHP 7.4:
   ```bash
   sudo a2dismod php8.2 && sudo a2enmod php7.4 && sudo systemctl restart apache2
   ```
3. **Database:**
   Since **zero schema modifications** were made, no database rollback or restore is required; the existing database continues to work seamlessly on Laravel 5.8.

