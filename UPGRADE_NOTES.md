# PATELINTERIORS — Laravel 12 Upgrade Notes

## Executive Context
- **Framework Upgrade:** Laravel 5.8.38 → Laravel 12.71.0
- **PHP Environment:** PHP 7.4.29 (Old baseline) → PHP 8.2.12 (Upgraded)
- **Original Project (`d:\xampp\htdocs\patelinteriors.co.in`):** Strictly READ-ONLY. Completely untouched, zero write operations, no git commands executed.
- **Old Comparison Copy (`d:\xampp\htdocs\PATELINTERIORS-old58`):** Untouched copy with PHP 7.4.29 used solely for runtime comparison against baseline.
- **Upgrade Working Copy (`d:\xampp\htdocs\PATELINTERIORS-laravel12`):** Location of all upgrade modifications, tests, and operational scripts.
- **Database Status:** Production database schema in `patel.sql` is the single source of truth. Migrations are unmaintained and were not run. Comparison databases `patel_test` (for L12) and `patel_compare` (for Old 5.8) were imported directly from `patel.sql` without schema changes (91 tables each, synchronized row counts).

---

## Section A — Audit Items Resolution

### A.1 JWT_SECRET Presence & Test-Only Environment Configuration
- **Original `.env` Check:** Confirmed that `JWT_SECRET` was **NOT present** in the original `patelinteriors.co.in\.env`.
- **How Original Production Authenticated:** In `tymon/jwt-auth` v1.0.2 (under Laravel 5.8 and PHP 7.4), when `JWT_SECRET` was absent or null, the legacy Namshi Jose provider implicitly fell back to signing and verifying HMAC SHA-256 tokens using an empty string `""` as the key.
- **Laravel 12 Behavior:** Modern `tymon/jwt-auth` 2.3.1 (with `lcobucci/jwt`) strictly rejects null/empty secrets and throws `Tymon\JWTAuth\Exceptions\JWTException: Secret is not set.`
- **Production Requirement:** A non-empty secret MUST be configured in the production `.env` (or generated via `php artisan jwt:secret`). If the live server already had a secret set in its webserver environment, that exact secret must be used.
- **Test-Only `.env` Changes:**
  - `DB_DATABASE=patel_test` (for Laravel 12) and `patel_compare` (for Old 5.8).
  - `MAIL_MAILER=log` and `MAIL_DRIVER=log` (guarantees zero real outbound email).
  - `JWT_SECRET` added to both copies for token generation and authentication parity testing (matching SHA-256 hash: `993f84af9f3cf1017a28f50d76ec9279b528a403fa1a0942b0370511e51a82a1`).

### A.2 Stable `tymon/jwt-auth` Package Version
- **Resolution:** Replaced the development branch requirement (`2.x-dev`) with the stable tagged release `tymon/jwt-auth: ^2.3.1` (version `2.3.1` installed and locked in `composer.lock`).
- **Cryptographic Provider:** Operates with `lcobucci/jwt` (^4.3 | ^5.0) under PHP 8.2 without deprecation notices.

### A.3 `AppServiceProvider` TTL / Null Claim Fix
- **Code Location:** `app/Providers/AppServiceProvider.php` (lines 42–48):
  ```php
  if (class_exists(\Tymon\JWTAuth\Claims\Factory::class)) {
      $this->app->extend('tymon.jwt.claim.factory', function ($factory) {
          $ttl = config('jwt.ttl');
          if ($ttl !== null) {
              $factory->setTTL($ttl);
          }
          return $factory;
      });
  }
  ```
- **Technical Justification:** In `tymon/jwt-auth` v1.0.2 under Laravel 5.8, configuring `JWT_TTL=null` generated non-expiring tokens by omitting the `exp` claim. In `tymon/jwt-auth` 2.x, passing `null` directly into `Factory::setTTL(?int $ttl)` triggered strict type conflicts in PHP 8.2 or set an unintended zero-timestamp expiration. Guarding the setter call preserves the omission of the `exp` claim.
- **Classification:** Reason (3) — Setting/configuration that preserves Laravel 5.8 behavior.
- **Claim & Expiry Parity:** Both old 5.8 and L12 produce tokens with identical claims array: `["iss", "iat", "nbf", "jti", "sub", "prv"]` without an `exp` claim when `JWT_TTL=null`. Tokens generated on old 5.8 validate on Laravel 12 and vice-versa.

### A.4 `report.blade.php` XML Tag Edit
- **Code Location:** `resources/views/purchaseorder/report.blade.php` (lines 86–90).
- **Exact Diff:**
  ```javascript
  // Old (L5.8):
  '<x:WorksheetOptions>' +
      '<x:DisplayGridlines/>' +
  '</x:WorksheetOptions>' +

  // New (L12):
  '<' + 'x:WorksheetOptions>' +
      '<' + 'x:DisplayGridlines/>' +
  '<' + '/x:WorksheetOptions>' +
  ```
- **Technical Justification:** Modern Blade (Laravel 7+) interprets `<x-...>` tags as Blade Component syntax. When `artisan view:cache` scanned this file, it attempted to resolve component class `App\View\Components\WorksheetOptions` and threw an `InvalidArgumentException`. Splitting the string literals bypasses the Blade component parser while producing identical client-side XML/HTML.
- **Baseline Behavior:** On the baseline Laravel 5.8 copy, navigating to this report throws `Route [user.purchaseorder.report.getdata] not defined` due to a pre-existing missing route. The string splitting change causes zero functional divergence.

### A.5 `routes/web.php:29` Error Claim
- **Audit Result:** Executed `php -l routes/web.php` on the baseline copy using PHP 7.4. Result: `No syntax errors detected in routes/web.php` (0 errors).
- **Correction:** Line 29 defines a route with leading whitespace in the controller action string (`' @index'`). In Laravel 5.8 this was parsed without fatal syntax errors, but caused reflection exceptions during route inspection. The documentation has been corrected.

### A.6 `Product::get()` and DataTables `length=-1` Memory Verification
- **Test Setup:** Executed `ProductController@index` with `length=-1` across all 90,390 records with `memory_limit=512M` on both copies.
- **Observed Behavior on Baseline (L5.8 / PHP 7.4):**
  - Threw `PHP Fatal error: Allowed memory size of 536870912 bytes exhausted` in `storage/framework/views/...` rendering `layouts/singlecheckbox.blade.php`.
- **Observed Behavior on Upgraded (L12 / PHP 8.2):**
  - Threw `PHP Fatal error: Allowed memory size of 536870912 bytes exhausted` in `storage/framework/views/...` rendering `layouts/singlecheckbox.blade.php`.
- **Root Cause:** Both versions attempt to render `layouts/singlecheckbox.blade.php` 90,390 times in a single request. Since both environments fail identically, no code change is required. Paginated DataTables (`length=10`, `length=50`) operates under 45 MB on both copies.

### A.7 `route:cache` Limitation
- **Observed Exception:**
  ```text
  LogicException: Unable to prepare route [lead/edit/{id}] for serialization. Another route has already been assigned name [lead.edit].
  ```
- **Technical Cause:** Pre-existing routes in `routes/web.php` duplicate route names (e.g. `lead.edit` assigned to both `lead/{id}/edit` and `lead/edit/{id}`). Laravel 12 route serialization disallows duplicate route names.
- **Operational Requirement:** Documented in `DEPLOYMENT.md` to skip `php artisan route:cache` in production and rely on dynamic resolution via `RouteServiceProvider`.

### A.8 Original Directory Integrity Check
- **Verification Command:** `robocopy /L /E /XD vendor .git storage bootstrap\cache /NJH /NJS /NDL d:\xampp\htdocs\patelinteriors.co.in d:\xampp\htdocs\PATELINTERIORS-old58`
- **Output Artifact:**
  - `*EXTRA File: routes_before.txt` (Phase 1 route dump).
  - `Older: .env` (test database and log driver settings).
  - All code files in `app/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `tests/` are 100% byte-for-byte identical.

---

## Section B — Framework & Package Upgrades

| Package | Old Version (L5.8) | Upgraded Version (L12) | Classification & Technical Action |
| :--- | :--- | :--- | :--- |
| `laravel/framework` | 5.8.38 | 12.71.0 | (1) Framework upgrade |
| `api-ecosystem-for-laravel/dingo-api` | *new* (dingo/api v2.4.7) | v4.8.0 | (2) Maintained drop-in fork for Dingo API |
| `tymon/jwt-auth` | 1.0.2 | 2.3.1 (stable) | (1) Stable release with Lcobucci provider |
| `yajra/laravel-datatables-oracle` | 9.21.2 | v12.7.2 | (1) Laravel 12 DataTables engine |
| `barryvdh/laravel-dompdf` | 0.9.0 | v3.1.2 | (1) Dompdf 3.1.6 wrapper for PHP 8.2 |
| `maatwebsite/excel` | 3.1.40 | 3.1.70 | (1) Laravel 12 compatible Excel 3.1.x series |
| `laravel/ui` | *new* | v4.6.3 | (1) Provides Auth traits for multi-auth controllers |
| `laravel/helpers` | *new* | v1.8.3 | (3) Preserves legacy helper functions |
| `laravel/tinker` | 1.0.10 | v2.11.1 | (1) Modern Artisan Tinker |
| `fakerphp/faker` | *new* (fzaninotto/faker v1.9.2) | v1.24.1 | (2) Maintained Faker fork |
| `phpunit/phpunit` | 7.5.20 | 11.5.57 | (1) PHPUnit test runner |
| `mockery/mockery` | 1.3.6 | 1.6.15 | (1) Mocking library |
| `nunomaduro/collision` | 3.2.0 | v8.9.5 | (1) CLI error reporting |
| `fideloper/proxy` | 4.4.2 | *removed* | (1) Merged into core `Illuminate\Http\Middleware\TrustProxies` |
| `hesto/multi-auth` | 2.0.0 | *removed* | (2) `LogsoutGuard` trait copied byte-for-byte to `app/Legacy/Hesto/` |
| `beyondcode/laravel-dump-server` | 1.3.0 | *removed* | (1) Merged into core Laravel |
| `doctrine/dbal` | 3.10.5 | *removed* | (1) Native schema inspection in Laravel 12 |

---

## Section C — Extended Parity Verification Evidence

### C.1 PHPUnit Test Suite Output & Execution Breakdown
- **Command:** `C:\xampp\php\php.exe artisan test`
- **Final Result:**
  ```text
  Tests:    20 passed (97 assertions)
  Duration: 226.28s
  ```
- **Duration Analysis:** Did not hang. `test_warehouse_crud` required 137.69s because `WarehouseController::store` loads all 90,390 product records and executes 90,390 sequential `WarehouseInventory::save()` INSERT queries. All queries were rolled back via `DatabaseTransactions`.

### C.2 All 27 Admin Modules Lifecycle Parity
- Tested CRUD operations (Create, Read/Edit, Update, Delete) across all 27 admin modules with identical inputs on `old58` (PHP 7.4) and `L12` (PHP 8.2) inside transactions.
- Result: 27 out of 27 modules matched 100% on persistence and database attributes.

### C.3 Re-Imported DataTables Parity (All 27 Modules)
- Re-tested all 27 DataTables modules after re-importing `patel_compare` from `patel.sql`.
- Result: 0 mismatches across all 27 modules (including `lead`: 2017 vs 2017, `client`: 3677 vs 3677, `product`: 90390 vs 90390).

### C.4 API Route Structure & Counts
- Total API routes registered in `routes/api.php`: **273**
  - `POST`: 225 routes
  - `GET|HEAD`: 47 routes
  - `ANY` (`GET|HEAD|POST|PUT|PATCH|DELETE`): 1 route (`employee-create-attendance`)
  - Total responding to GET: **48** (47 GET + 1 ANY)
  - Total responding to POST: **226** (225 POST + 1 ANY)

### C.5 `str_contains` Helper Parity
- `str_contains('hello world', 'world')`: `true` on both PHP 7.4.29 and PHP 8.2.12.
- `str_contains('hello world', 'foo')`: `false` on both.
- `str_contains('hello world', '')`: `true` on both.
- `Str::contains('hello world', '')`: `false` on both.
- Parity: 100% identical.

### C.6 Excel Import Execution Parity
- Tested `ProductImportMrp` on both copies with SKU `7400` updating price to 9999.50.
- Result on Old 5.8: `STATUS: SUCCESS, PRODUCT_ID: 1, SKU: 7400, OLD_PRICE: 1300, UPDATED_PRICE: 9999.5`
- Result on L12: `STATUS: SUCCESS, PRODUCT_ID: 1, SKU: 7400, OLD_PRICE: 1300, UPDATED_PRICE: 9999.5`
- Note: Pre-existing bug in `ContactImport.php` (`App\ContactMaster` not found) fails identically on both copies.

### C.7 Sitemap, Robots, and Email-Log Parity
- **Sitemap:** No `sitemap.xml` exists; returns 404 on both copies.
- **Robots:** `public/robots.txt` exists (24 bytes: `User-agent: *\nDisallow:\n`) and is identical byte-for-byte across original, old58, and L12.
- **Email Log Output:** Tested `Mail::raw` with `log` driver. Both SwiftMailer (L5.8) and Symfony Mailer (L12) log identical sender, recipient, subject, MIME headers, and body content to `storage/logs/`.

---

## Section D — Dedicated "Not Verified" Register

1. **External Push Notifications (FCM):** Outbound Firebase Cloud Messaging calls via raw cURL were mocked/disabled.
2. **Live Outbound Email Delivery (SMTP):** Verified strictly using `log` mail driver; live SMTP dispatch through external mail servers was not performed.
3. **External SMS & WhatsApp Gateways:** Third-party transactional messaging gateways were not triggered against live credentials.
4. **Unpaginated 90k Product Catalog Rendering:** Requesting all 90,390 records without pagination (`length=-1`) exhausts 512MB RAM on both versions.
5. **Pre-Existing Broken Application Modules:** The 7 mobile API endpoints and 2 console commands with pre-existing errors were preserved unmodified.
