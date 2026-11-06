# PATELINTERIORS — QA Verification & Manual Test Checklist

This document details the verification status of the Laravel 12 / PHP 8.2 upgrade for **Patel Interiors**, distinguishing between automated parity test suite verifications and items requiring manual browser/device QA before final production sign-off.

> **Environment Setup for Testing:**
> - Server Startup Script: `start-patel-l12.bat` in the project root.
> - Web URL: `http://127.0.0.1:8007`
> - PHP Version: `8.2.12`
> - Database: `patel_test` (populated from `patel.sql`)
> - Outbound Mail: Set to `log` driver (`MAIL_MAILER=log` in `.env`), ensuring zero real emails are sent.

---

## 1. Authentication & Session Management

| Feature | Automated Verification | Manual QA Action Required | Status |
| :--- | :--- | :--- | :--- |
| **Public Root (`/`)** | Verified 200 OK; HTML diff confirms exact template rendering (diff only in CSRF attributes). | Navigate to `http://127.0.0.1:8007/` and verify the sign-in form displays correctly. | Ready for QA |
| **Admin Login (`/user/login`)** | Session authentication tested via HTTP feature tests with multi-guard (`user`, `web`). | Log in with administrative credentials. Verify redirection to `/user/dashboard`. | Pending Manual QA |
| **Employee Login (`/employee/login`)** | Route accessibility and view rendering verified with 200 OK. | Log in with employee credentials. Verify redirection to `/employee/home`. | Pending Manual QA |
| **Logout Operations** | Route definitions and middleware chains tested. | Click logout in the top-right profile dropdown. Verify session termination and redirect to login. | Pending Manual QA |

---

## 2. Core Modules & DataTables Parity

The 27 modules below were verified against the Laravel 5.8 baseline copy across route status, DataTables JSON response schema, record counts, and action button URL structures.

| Module | URL | Automated Status | Manual QA Checklist |
| :--- | :--- | :--- | :--- |
| **Leads & CRM** | `/user/lead` | 2,017 records verified; DataTables schema matches. | [ ] Filter by stage/branch, search by phone, pagination |
| **Clients & KYC** | `/user/client` | 3,677 records verified; DataTables schema matches. | [ ] Client profile modal, view document attachments |
| **Products** | `/user/product/index` | 90,390 records verified; paginated DataTables loads. | [ ] Paginated search by SKU/name, category filter dropdown |
| **Product Categories**| `/user/productcategory`| 37 records verified; active toggles match. | [ ] Create category, toggle status switch |
| **Companies / Brands**| `/user/company` | 257 records verified. | [ ] Add company, branch association dropdown |
| **Followup Types** | `/user/type` | 20 records verified; CRUD lifecycle automated. | [ ] Create/edit followup type |
| **Brand Logos** | `/user/brandlogo` | 3 records verified; thumbnail URLs match. | [ ] Upload brand logo image |
| **Source Names** | `/user/sourcename` | 259 records verified. | [ ] Filter sources, status toggle |
| **Quotations** | `/user/quotations` | 0 records verified (empty state parity confirmed).| [ ] Create new quotation draft |
| **Sales Orders** | `/user/salesorder` | 901 records verified. | [ ] View sales order items, print/export view |
| **Purchase Orders** | `/user/purchaseorder` | 9 records verified. | [ ] PO listing, link to vendor |
| **Purchase Invoices** | `/user/purchase/invoice/index`| 2,164 records verified. | [ ] Invoice details modal, itemized breakdown |
| **Warehouses** | `/user/warehouse` | 15 records verified; CRUD lifecycle automated. | [ ] Add warehouse, assign branch |
| **Interior Visits** | `/user/visits` | 147 records verified. | [ ] View site visit entry, photos preview |
| **Service & Claims** | `/user/service` | 822 records verified. | [ ] Service request status update |
| **Complaints** | `/user/complains` | 0 records verified (empty state parity confirmed).| [ ] Add test complaint entry |
| **Attendance & GPS** | `/user/attendance/index`| 5,731 records verified. | [ ] Date range filter, GPS coordinate display |
| **Leave Management** | `/user/leave` | 4 records verified. | [ ] Review leave request, approve/reject |
| **Appointment Letters**| `/user/appointment_letter`| 1 record verified; PDF route verified. | [ ] Download appointment letter PDF |
| **Company Policies** | `/user/company_policy` | 1 record verified; 17-page PDF verified. | [ ] Download company policy PDF |
| **Expenses** | `/user/expense` | Index 200 OK verified; empty state parity confirmed. | [ ] Add expense voucher, category select |
| **Holidays** | `/user/holiday` | 4 records verified; CRUD lifecycle automated. | [ ] Add holiday to annual calendar |
| **Roles & Permissions**| `/user/role` | 5 records verified; permissions matrix matches. | [ ] Inspect role permission checkboxes |
| **Users / Admins** | `/user/user/index` | 6 records verified; multi-guard access confirmed. | [ ] Superadmin and branch admin listings |
| **Employees** | `/user/employee/index` | 21 records verified. | [ ] Staff directory, branch filter |
| **Vendors** | `/user/vendor` | 180 records verified; CRUD lifecycle automated. | [ ] Vendor details, GSTIN validation |
| **Tasks** | `/user/task` | 6 records verified. | [ ] Assign task to staff, status update |

---

## 3. CRUD Lifecycle Operations

- **Create Record:**
  - *Automated:* Verified via `AdminModulesFeatureTest::test_type_crud_lifecycle` (`POST /user/type`).
  - *Manual QA:* Go to `/user/type/create`, enter "QA Test Type", click Submit. Confirm flash message and appearance in table.
- **Edit / Update Record:**
  - *Automated:* Verified via `AdminModulesFeatureTest` (`PUT /user/type/{id}`).
  - *Manual QA:* Click edit icon on newly created row, rename to "QA Test Type Updated", submit and verify persistence.
- **Delete Record:**
  - *Automated:* Verified via `AdminModulesFeatureTest` (`GET /user/deletestype/clones/{id}`).
  - *Manual QA:* Click delete icon, accept confirmation prompt, verify row is removed.
- **Batch / AJAX Status Toggle:**
  - *Automated:* Verified via `AdminModulesFeatureTest::test_productcategory_status_change`.
  - *Manual QA:* Click status toggle on any module row, verify state changes without full page reload.

---

## 4. Reports & Document Generation (PDF & Excel)

- **DomPDF — Appointment Letter (`/user/appointment-letter/pdf/1`):**
  - *Automated:* Verified valid `%PDF-1.7` stream, single page (1,135 bytes).
  - *Manual QA:* Open URL in browser tab. Confirm text and formatting render cleanly.
- **DomPDF — Company Policy (`/user/company-policy/pdf/1`):**
  - *Automated:* Verified valid `%PDF-1.7` stream, 17 pages.
  - *Manual QA:* Open URL in browser tab. Verify all 17 pages scroll smoothly without font or layout artifacts.
- **Excel — Gallery Export:**
  - *Automated:* Verified column headers and data collection match baseline.
  - *Manual QA:* Download export file, open in Excel or LibreOffice, check headings and rows.
- **Excel — Product MRP Export:**
  - *Automated:* Verified via `AdminModulesFeatureTest`.
  - *Manual QA:* Trigger export from `/user/product/index`. Open generated `.xlsx` file and inspect column headers.

---

## 5. Mobile & API Endpoints (Dingo API + JWT Auth)

- **JWT Authentication & Token Validation:**
  - *Automated:* Validated token issuance, claim generation (`["iss","iat","nbf","jti","sub","prv"]` without `exp` when `JWT_TTL=null`), and cross-version token verification.
- **User Profile Endpoint (`GET /api/v1/get-user`):**
  - *Automated:* Verified 200 OK and JSON structure matching baseline.
- **Mobile Staging Verification:**
  - *Manual QA:* Point mobile staging app to `http://<HOST_IP>:8007/api/v1/` and verify:
    - [ ] Field employee authentication
    - [ ] Attendance punch-in with geolocation coordinates
    - [ ] Lead follow-up entry submission
    - [ ] Site visit photo upload

---

## 6. Console Commands & Background Cron

- **`AttendanceCron:check`:**
  - *Automated:* Executed successfully; attendance notifications logged to `storage/logs/laravel-YYYY-MM-DD.log`.
- **`FollowupCron:check`:**
  - *Pre-existing Behavior:* Fails identically on baseline and L12 (`View [lead.followup_email] not found`). Preserved unmodified.
- **`Export:check`:**
  - *Pre-existing Behavior:* Fails identically on baseline and L12 when run directly via CLI due to missing `Auth::user()` context. Preserved unmodified.

---

## 7. Sign-Off & Acceptance Register

| Role | Name | Date | Status |
| :--- | :--- | :--- | :--- |
| **Upgrade Engineer** | Antigravity AI | 2026-11-06 | Parity Verified per Evidence |
| **QA Engineer** | | | [ ] Pending Manual Testing |
| **Product Owner** | | | [ ] Pending Acceptance |
