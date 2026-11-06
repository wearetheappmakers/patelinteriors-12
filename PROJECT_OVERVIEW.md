# PATELINTERIORS — Project Technical Overview

## 1. Basics
- **Laravel & PHP:** Laravel Framework **5.8.38** on PHP **^7.1.3** (runs on local PHP 7.4.29; fails on PHP 8.2 without upgrade).
- **Database Config (`config/database.php`):** MySQL connection `charset` = `utf8mb4`, `collation` = `utf8mb4_unicode_ci`, `strict` = `true`.
- **Drivers:** Mail: `smtp`, Filesystem: `local`, Cache: `file`, Session: `file`, Queue: `sync`. Timezone: `Asia/Calcutta`.
- **Vendor Directory:** `vendor/` exists (`True`).
- **Application Key (`.env`):** `APP_KEY` is set (`YES`).
- **Database Name:** `patel` (Exists locally in MariaDB: `YES`).

## 2. Environment
- **PHP 8.2 Binary:** `C:\xampp\php\php.exe` (v8.2.12) | `memory_limit` = 512M, `max_execution_time` = 0.
- **PHP 7.4 Binary:** `D:\xampp\php\php.exe` (v7.4.29) | `memory_limit` = 512M, `max_execution_time` = 0.
- **Extensions (both versions):** `gd` (Yes), `zip` (Yes), `bcmath` (Yes), `fileinfo` (Yes), `curl` (Yes), `openssl` (Yes); `imagick` (No), `intl` (No), `gmp` (No).
- **Local Database Server:** MariaDB 10.4.32 (`C:\xampp\mysql\bin\mysql.exe`).
- **Storage Symlink:** `public/storage` does NOT exist (uploads stored directly in `public/` subdirectories).

## 3. Packages & Laravel 12 Compatibility
| Package | Installed | Target L12 / PHP 8.2 Version | Risk & Upgrade Path |
| :--- | :--- | :--- | :--- |
| `laravel/framework` | `v5.8.38` | `^12.0` | High; multi-step syntax/core migration |
| `dingo/api` | `v2.4.7` | *No compatible version* | **CRITICAL**; abandoned. Migrate to native Laravel 12 API routes & resources |
| `tymon/jwt-auth` | `1.0.2` | `^2.1` or `php-open-source-saver/jwt-auth:^2.2` | **HIGH**; v1.0.2 uses `namshi/jose` (PHP 8.2 broken). Migrate to drop-in fork or Sanctum |
| `hesto/multi-auth` | `v2.0.0` | *None needed* | Medium; dev scaffolding package, remove from composer. Multi-auth is native in Laravel |
| `yajra/laravel-datatables-oracle` | `v9.21.2` | `^12.0` | High; breaking changes in collection/engine signatures |
| `maatwebsite/excel` | `3.1.40` | `^3.1.58` | Low/Medium; compatible with PHP 8.2 / PhpSpreadsheet 2.x |
| `barryvdh/laravel-dompdf` | `v0.9.0` | `^3.1.0` | Medium; upgrade to Dompdf 3.x for PHP 8.2 compatibility |
| `fideloper/proxy` | `4.4.2` | *Removed* | Low; remove package, use native `Illuminate\Http\Middleware\TrustProxies` |
| `doctrine/dbal` | `3.10.5` | `^4.0` or none | Low; Laravel 11+ no longer requires DBAL for schema modifications |
| `beyondcode/laravel-dump-server` | `1.3.0` | *Removed* | Dev package; obsolete (built into Laravel core) |
| `fzaninotto/faker` | `v1.9.2` | `fakerphp/faker:^1.23` | Dev package; abandoned, replace with modern fork |

## 4. Website (Public Side)
- **Public Pages:** No public customer-facing frontend. Root route `/` renders `auth.login`. Internal ERP/CRM system only.
- **SEO & Sitemaps:** No meta tags, no SEO slugs, no sitemap (`public/sitemap.xml` absent). `robots.txt` contains default blank `User-agent: * Disallow:`.
- **Public Contact Forms:** None. All lead/contact submissions require authenticated session or API JWT token.

## 5. Admin & Frontend Tech
- **Template:** Metronic Responsive Admin Dashboard (Bootstrap 4.1.0, jQuery 3.2, Popper.js).
- **Build Tool:** Laravel Mix v4.0.7 (`webpack.mix.js` compiles `resources/sass/app.scss` and `resources/js/app.js`).
- **Plugins:** DataTables (Metronic bundle), Select2, FullCalendar, Toastr, Google Maps JS API (`AIzaSy...` in footer).
- **Rich Text Editors:** CKEditor 4 (`cdn.ckeditor.com/4.14.1/standard/ckeditor.js` & local LTS 4.25.1) used in Company Policy, Task, and Lead Communication. No image upload endpoints configured (plain HTML markup stored in DB).
- **Pagination:** AJAX DataTables pagination; standard Bootstrap paginator.

## 6. Auth & Roles
- **Guards (`config/auth.php`):**
  - `web` / `user`: `session` driver using `App\User` (users table; represents branch admins / headquarters).
  - `employee`: `session` driver using `App\Employee` (employees table; represents staff/field executives).
  - `api`: `jwt` driver using `App\User`.
- **Middleware:** `RedirectIfUser`, `RedirectIfNotUser`, `RedirectIfEmployee`, `RedirectIfNotEmployee`, `JwtMiddleware` (`jwt.verify`).
- **Roles/Permissions:** Custom `role` table and permissions logic (spatie/permission is NOT installed).
- **Security Vulnerability:** Plain-text password mirror columns exist in database:
  - `users.show_password` (`varchar(255)`)
  - `employees.confirm_password` (`varchar(255)`)

## 7. API Architecture
- **Framework:** `dingo/api` router loaded via `app('Dingo\Api\Routing\Router')`.
- **Prefix & Version:** `/api/v1` (`API_STANDARDS_TREE=x`, `API_SUBTYPE=patelinterior`, `API_STRICT=true`).
- **Auth:** `jwt.verify` middleware (`tymon/jwt-auth`). Login routes: `POST /api/v1/user-login`, `POST /api/v1/employee-login`.
- **Token TTL:** `JWT_TTL=NULL` in `config/jwt.php` (never expire unless configured). Refresh TTL = 20,160 min (14 days). Algorithm: `HS256`.
- **Client Application:** Dedicated Mobile App for field employees and branch supervisors (GPS attendance check-in, lead followups, visits, photo uploads, complaints).

## 8. External Integrations
- **Push Notifications:** Firebase Cloud Messaging (FCM) legacy endpoint (`https://fcm.googleapis.com/fcm/send`) via raw `curl_init` in 12 controllers and 2 cron commands using `env('Employee_server')`. Deprecated and disabled by Google since June 2024.
- **Email:** SMTP via SwiftMailer (`MAIL_DRIVER=smtp`). Sends followup notifications and attendance alerts.
- **Maps:** Google Maps JavaScript API script loaded in Metronic layout footer.
- **Payment / SMS / Cloud Storage:** None (no Razorpay/Paytm/Stripe; no SMS gateways; files saved locally to `public/`).
- **CSRF Exceptions:** `VerifyCsrfToken::$except` is empty.

## 9. Key Modules Matrix
| Module | Controller | Key Model(s) | Key Table(s) | CRUD / Ops | Public | PDF | Upload | Import | Export | Mail | AJAX | API |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Auth (Branch/HQ)** | `UserAuth\*` | `User` | `users`, `user_password_resets` | L/C/E/D | No | No | No | No | No | Yes | Yes | Yes |
| **Auth (Employee)** | `EmployeeAuth\*` | `Employee` | `employees`, `employee_password_resets` | L/C/E/D | No | No | No | No | No | Yes | Yes | Yes |
| **Leads & CRM** | `LeadController` | `Lead`, `LeadFollowUp` | `lead`, `lead_followup`, `lead_assign` | L/C/E/D/S | No | Yes | Yes | No | Yes | Yes | Yes | Yes |
| **Clients & KYC** | `ClientController` | `Client`, `ClientDocument` | `client`, `client_document` | L/C/E/D/S | No | No | Yes | Yes | Yes | No | Yes | Yes |
| **Quotations** | `QuotationController` | `Quotation`, `QuotationChild` | `quotation`, `quotation_child` | L/C/E/D/S | No | Yes | No | No | No | No | Yes | Yes |
| **Revised Quotes** | `RevisedController` | `RevisedQuotation`, `RevisedChild`| `revised_quotation`, `revised_child` | L/C/E/D/S | No | Yes | No | No | No | No | Yes | No |
| **Sales Orders** | `SalesOrderController` | `SalesOrder`, `SalesOrderChild` | `salesorder`, `salesorder_child` | L/C/E/D/S | No | No | No | Yes | No | No | Yes | Yes |
| **Invoicing** | `SalesOrderInvoiceController` | `SalesOrderInvoice` | `salesorder_invoice`, `salesorder_invoice_child` | L/C/E/D/S | No | No | No | Yes | No | No | Yes | No |
| **Purchase & PO** | `PurchaseOrderController` | `PurchaseOrder` | `purchase_order`, `purchase_order_child` | L/C/E/D/S | No | No | No | No | No | No | Yes | No |
| **Purchase Invoices** | `PurchaseInvoiceController`| `PurchaseInvoice` | `purchase_invoice`, `purchase_invoice_child` | L/C/E/D/S | No | No | No | No | No | No | Yes | No |
| **Products & Cat.** | `ProductController` | `Product`, `ProductCategory` | `product`, `product_category` | L/C/E/D/S | No | No | No | Yes | Yes | No | Yes | Yes |
| **Warehouses & Inv.** | `WarehouseController` | `Warehouse`, `WarehouseInventory` | `warehouses`, `warehouse_inventories` | L/C/E/D/S | No | No | No | No | Yes | No | Yes | No |
| **Stock Opening** | `OpeningStockController` | `OpeningStock` | `opening_stock`, `opening_stock_child` | L/C/E/D/S | No | No | No | Yes | No | No | Yes | No |
| **Interior Visits** | `VisitsController` | `Visits`, `VisitsChild` | `interior_visit`, `interior_visit_child` | L/C/E/D/S | No | Yes | Yes | No | Yes | No | Yes | Yes |
| **Service & Claims** | `ServiceController` | `Service`, `ServiceChild` | `service`, `service_child`, `service_image` | L/C/E/D/S | No | No | Yes | No | No | No | Yes | Yes |
| **Complaints** | `ComplainController` | `Complain` | `complain` | L/C/E/S | No | No | Yes | No | No | No | Yes | Yes |
| **Attendance & GPS** | `AttendanceController` | `Attendance` | `attendance` | L/C/S | No | No | Yes | No | No | Yes | Yes | Yes |
| **HR & Letters** | `AppointmentLetterController`| `AppointmentLetter` | `appointment_letter` | L/C/E/D/S | No | Yes | Yes | No | No | No | Yes | No |
| **Policy Documents** | `CompanyPolicyController` | `CompanyPolicy` | `company_policy` | L/C/E/D/S | No | Yes | No | No | No | No | Yes | No |
| **Expenses & Cash** | `ExpenseController` | `Expense`, `ExpenseChild` | `expense`, `expense_child` | L/C/E/D/S | No | No | No | No | No | No | Yes | No |
| **Credit / Debit** | `CreditController`, `DebitController` | `Credit`, `Debit` | `credit`, `debit`, `credit_child`, `debit_child` | L/C/E/D/S | No | No | No | No | No | No | Yes | No |
| **Reports** | `ReportController` | `ItemLedger`, `StockTransferHistory`| `item_ledger`, `stock_transfer_history` | L/S | No | Yes | No | No | Yes | No | Yes | No |

## 10. Route Analysis
- **Route Definitions per File:**
  - `routes/web.php`: 537 lines | 391 routes (323 `'Controller@method'`, 66 `Route::resource`).
  - `routes/api.php`: 338 lines | 279 Dingo routes (280 `'Controller@method'`).
  - `routes/user.php`: 13 lines | 1 active route (`HomeController@index`).
  - `routes/employee.php`: 4 lines | 1 active route (`HomeController@index`).
- **Syntax Bug (`routes/web.php:29`):** Malformed route `Route::get('/index/', ' @index')` crashes `php artisan route:list` with `ReflectionException: Class App\Http\Controllers\ does not exist`. Should be `ProductController@index`.
- **Missing Controller Classes (4 routes):**
  - `AMCController` (missing in `routes/web.php:65`)
  - `ContactMasterController` (missing in `routes/web.php:94, 95`)
  - `DistributorController` (missing in `routes/web.php:191`)
- **Missing Controller Methods (18 methods):**
  - `HomeController`: `discountMultiple` (line 114), `changeOrder` (115), `multiStatus` (116), `upload` (120)
  - `VendorController@delete` (line 241), `PMCController@delete` (242)
  - `AttendanceController@image` (line 313), `ReportController@quotationreport` (344)
  - `LeaveController@approved` (line 374), `ProductController@getemployee` (377)
  - `EmployeeAuth`: `RegisterController@register`, `ForgotPasswordController@sendResetLinkEmail`, `ResetPasswordController@reset`
  - `UserAuth`: `LoginController@logout`, `RegisterController@register`, `ForgotPasswordController@sendResetLinkEmail`, `ResetPasswordController@reset`
  - `Api\V1\EmployeeLoginController@employees_getquotations` (`routes/api.php:188`)
- **Duplicate Route Names:** 54 duplicates caused by reused names across un-prefixed groups (`index`, `create`, `store`, `edit`, `update`, `show`) and identical names in `employee/` vs `user/` auth (`login`, `logout`, `register`, `password.request`).

## 11. URL Generation Risks
- **Total `route()` / `action()` calls:** 1,109 across controllers and views.
- **Parameter Mismatches / Null Value Risks (22 calls across 11 files):**
  - `resources/views/layouts/actionbtnpermission.blade.php`: 7 calls using dynamic `$route` interpolation (`route('user.'.$route.'.edit', $id)`).
  - `app/Http/Controllers/InventoryController.php`: 2 calls.
  - `app/Http/Controllers/QuotationController.php`: 2 calls.
  - `resources/views/delivery/index.blade.php`: 2 calls.
  - `resources/views/report/stock_report.blade.php`: 2 calls.
  - `resources/views/revised/index.blade.php`: 2 calls.
  - Single calls in `delivery/log.blade.php`, `gallery/edit.blade.php`, `openingstock/index.blade.php`, `purchaseinvoice/view.blade.php`, `quotations/clone.blade.php`.

## 12. Models vs Database Schema
- **Models:** 86 models in `app/*.php`.
- **Missing Database Table:** `Detail` model (`app/Detail.php`) defines `protected $table = 'details'`, which does NOT exist in `patel.sql` or MariaDB (orphaned model).
- **Custom Table Names:** `Visits` (`interior_visit`), `VisitsChild` (`interior_visit_child`), `LeadFollowUp` (`lead_followup`), `LeadToshow` (`lead_toshow`), `Group` (`group`).
- **Reserved Word Hazard:** `Group` model targets SQL reserved keyword `group`.
- **Eloquent Attributes:** No models use `$dates = [...]`. No models use `SoftDeletes`.
- **Direct DB Queries:** 28 distinct tables bypassed via `DB::table(...)` (e.g. `warehouse_inventories`, `quotation`, `interior_visit`, `service`, `salesorder_child`, `pdfs`). Notably, `layouts/actionbtnpermission.blade.php` executes raw `DB::table()` queries directly inside view loops.

## 13. Database Review (`patel.sql`)
- **Summary:** 91 tables, 0 views, 0 triggers, 0 stored procedures/routines. 82 Foreign Key constraints.
- **Server Version & Dump Header:** Dumped from MariaDB `10.4.28-MariaDB` with `SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"`. Local server is `10.4.32-MariaDB`.
- **Engines:** All 91 tables use `InnoDB` (0 MyISAM tables).
- **Mixed Character Sets / Collations:**
  - 44 tables use `latin1` / `latin1_swedish_ci` (e.g. `warehouse_inventories`, `purchase_invoice`, `appointment_letter`, `service`).
  - 47 tables use `utf8mb4` / `utf8mb4_unicode_ci` (e.g. `product`, `lead`, `client`, `attendance`, `users`).
  - Causes MySQL collation mismatch errors during cross-table `JOIN` operations.
- **MySQL 8 Incompatible Collations:** None (0 tables use `utf8mb4_0900_ai_ci`).
- **Zero Date Hazard:** `service_image.updated_at` has `DEFAULT '0000-00-00 00:00:00'`, violating MySQL 5.7+ / 8.0 `NO_ZERO_DATE` strict mode.
- **`NOT NULL` without Defaults:** 132 columns across 31 tables lack default values.
- **Money / Financial Precision Hazard:** 76 financial columns (`amount`, `price`, `basic_month`, `net_salary_month`, `total_deductions_annual`) stored as `varchar(255)` rather than `DECIMAL(12,2)`.
- **Phone Numbers:** Stored as `varchar(255)` (no `int(11)` truncation hazard).
- **Top Tables by Volume:** `warehouse_inventories` (~1,155,027 rows), `product` (~87,825 rows), `city` (~47,101 rows), `purchase_invoice_child` (~6,715 rows), `followup_log` (~6,582 rows), `attendance` (~5,686 rows), `item_ledger` (~5,224 rows).

## 14. PDF, QR, Barcode & Media Logic
- **PDF Generation:** Powered by `barryvdh/laravel-dompdf:^0.9.0` (`dompdf/dompdf:v1.2.2`).
  - Used in 6 controllers: `AppointmentLetterController`, `CompanyPolicyController`, `PdfController`, `QuotationController`, `ReportController`, `VisitsController`.
  - 19 dedicated PDF Blade views (`resources/views/pdf*.blade.php`, `quotationpdf*.blade.php`, `report/*pdf.blade.php`, etc.).
  - Uses `PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])` for external images/fonts.
- **QR / Barcode:** No QR or barcode packages installed or utilized.
- **Media & File Storage:** Files uploaded via raw `$file->move(public_path(...))` directly to unversioned folders under `public/` (`uploads/signature/`, `brand_logo/`, `aadharimage/`, `panimage/`, `service/`). No Intervention Image manipulation, resizing, or thumbnails.

## 15. Helpers, Custom Code & Scheduling
- **Custom Helpers:** None (no `app/helpers.php` or helper autoloading in `composer.json`).
- **Middleware:** 12 classes in `app/Http/Middleware/` including `JwtMiddleware` and multi-auth redirectors.
- **Console Scheduler (`app/Console/Kernel.php`):**
  - `FollowupCron:check` (`everyMinute`): Queries `lead_followup`, sends emails via `Mail::send('lead.followup_email')` and legacy FCM push notifications.
  - `Export:check` (`everyMinute`): Generates Excel file via `Excel::store(new ProductExportMrp(), 'public/exports/...')` and writes to `download` table.
  - `AttendanceCron:check` (`everyMinute`): Queries unmarked attendance, sends alert emails and FCM push notifications to employees.
  - *Warning:* Running scheduler triggers live external emails and disk writes every 60 seconds.

## 16. Upgrade Risk Checklist (Laravel 12 / PHP 8.2)
1. **Dingo API Framework (`routes/api.php`):** Dingo has no Laravel 12 compatibility. Must be refactored to native Laravel routing and API resources.
2. **JWT Authentication (`app/Http/Middleware/JwtMiddleware.php`):** `tymon/jwt-auth:1.0.2` relies on `namshi/jose` (incompatible with PHP 8.2). Must switch to `php-open-source-saver/jwt-auth:^2.2` or Laravel Sanctum.
3. **Dead Legacy FCM Push Endpoint:** `https://fcm.googleapis.com/fcm/send` used in 12 controllers and 2 crons is decommissioned by Google. Must migrate to Firebase HTTP v1 API with service account credentials.
4. **TrustProxies Middleware (`app/Http/Middleware/TrustProxies.php`):** Uses abandoned `fideloper/proxy` and deprecated constant `Request::HEADER_X_FORWARDED_ALL`. Replace with core Laravel 12 `Illuminate\Http\Middleware\TrustProxies`.
5. **Route Syntax Error:** `routes/web.php:29` `' @index'` crashes artisan route reflection.
6. **Carbon & PHP 8.2 Deprecations:** `diffInDays()` in `BranchController` and `EmployeeLoginController`; dynamic properties on models without `#[AllowDynamicProperties]`.
7. **Zero Date & Strict Mode:** `service_image.updated_at` zero default causes SQL failures on modern MySQL/MariaDB.
8. **Mixed Collation Failures:** 44 `latin1` tables vs 47 `utf8mb4` tables cause JOIN failures under strict MySQL 8 / modern MariaDB.
9. **Yajra DataTables:** Version 9.x to 12.x upgrade requires updating method call signatures in 40+ controllers.
10. **SwiftMailer to Symfony Mailer:** Laravel 9+ replaces SwiftMailer with Symfony Mailer; custom `$message` closures in `FollowupCron`, `AttendanceCron`, and `ClientController` must be checked.

## 17. Open Questions Prior to Upgrade
1. **API Refactoring:** Should Dingo API be converted to standard Laravel 12 native API routes (`routes/api.php`) with Laravel Sanctum tokens or retain JWT with `php-open-source-saver/jwt-auth` for mobile app compatibility?
2. **Push Notifications (FCM v1):** Is the Firebase Service Account JSON credentials file available to upgrade the 14 FCM push call sites to HTTP v1?
3. **Collation Normalization:** Can we convert all 44 `latin1` tables to `utf8mb4_unicode_ci` during migration to prevent JOIN collation conflicts?
4. **Plain-Text Passwords:** Should `users.show_password` and `employees.confirm_password` columns be safely pruned or retained for legacy admin visibility?
5. **Route Cleanup:** Should the 3 missing controllers (`AMCController`, `ContactMasterController`, `DistributorController`) and orphaned routes be deleted or stubbed?
6. **Scheduled Crons:** Can the 1-minute execution frequency for `Export:check` and notification crons be safely throttled during development to prevent notification flood?

