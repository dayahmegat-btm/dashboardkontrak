# Project Task Breakdown
## Sistem Pengurusan Dokumen Kontrak & Bon Pelaksanaan

**Versi:** 1.0
**Tarikh:** 12 Mei 2026
**Total Duration:** 38 minggu (≈ 9 bulan)

---

## Daftar Kandungan

1. [Project Overview](#project-overview)
2. [Phase 0: Inisiasi (2 minggu)](#phase-0-inisiasi-2-minggu)
3. [Phase 1: Senibina & Reka Bentuk (4 minggu)](#phase-1-senibina--reka-bentuk-4-minggu)
4. [Phase 2: Sprint 1 - Auth & RBAC (4 minggu)](#phase-2-sprint-1---auth--rbac-4-minggu)
5. [Phase 3: Sprint 2 - SST & Kontrak (4 minggu)](#phase-3-sprint-2---sst--kontrak-4-minggu)
6. [Phase 4: Sprint 3 - Bon & Penilaian (4 minggu)](#phase-4-sprint-3---bon--penilaian-4-minggu)
7. [Phase 5: Sprint 4 - Dashboard & Alerts (5 minggu)](#phase-5-sprint-4---dashboard--alerts-5-minggu)
8. [Phase 6: PWA & Mobile (3 minggu)](#phase-6-pwa--mobile-3-minggu)
9. [Phase 7: Integrasi (3 minggu)](#phase-7-integrasi-3-minggu)
10. [Phase 8: UAT & Pembetulan (4 minggu)](#phase-8-uat--pembetulan-4-minggu)
11. [Phase 9: Migrasi Data & Latihan (3 minggu)](#phase-9-migrasi-data--latihan-3-minggu)
12. [Phase 10: Go-Live & Sokongan (2+ minggu)](#phase-10-go-live--sokongan-2-minggu)

---

## Project Overview

### Key Deliverables

- **Platform:** Laravel 11.x + FilamentPHP 3.x Admin Panel
- **Database:** MySQL 8.0 (28 tables)
- **Frontend:** TALL Stack (Tailwind + Alpine.js + Livewire + Laravel)
- **Mobile:** Progressive Web Application (PWA) with push notifications
- **Users:** 100-138 pengguna aktif across 7 roles
- **Data:** 170+ kontrak aktif worth RM 18+ million

### Success Criteria

- [ ] All functional requirements (FR-M1 to FR-M9) implemented
- [ ] 18 alert rules operational
- [ ] PWA Lighthouse score ≥ 90
- [ ] Unit test coverage ≥ 70% for critical modules
- [ ] UAT sign-off from all user groups
- [ ] Security penetration testing passed
- [ ] Data migration completed with PIC verification
- [ ] Training completed for all user groups

---

## Phase 0: Inisiasi (2 minggu)

### Week 1: Project Setup

#### **TASK-001: Project Kick-off Meeting** ⭐
- [ ] Schedule meeting with all stakeholders
- [ ] Review PRD, ERD, rules.md
- [ ] Confirm project timeline and milestones
- [ ] Assign team roles and responsibilities
- [ ] Setup communication channels (Slack, WhatsApp)
- [ ] **Deliverable:** Meeting minutes, signed attendance

#### **TASK-002: Development Environment Setup**
- [ ] Provision development servers (8 vCPU, 16GB RAM)
- [ ] Install PHP 8.2, Composer, Node.js, npm
- [ ] Install MySQL 8.0, Redis 7.x
- [ ] Configure Nginx 1.24+
- [ ] Setup Git repository (GitHub/GitLab)
- [ ] Configure `.env.example` file
- [ ] **Deliverable:** Working dev environment

#### **TASK-003: Laravel Project Initialization**
- [ ] Create new Laravel 11.x project: `composer create-project laravel/laravel dashboardkontrak`
- [ ] Configure database connection
- [ ] Install Vite and configure: `npm install`
- [ ] Setup `.editorconfig` for team consistency
- [ ] Configure Git hooks (pre-commit, pre-push)
- [ ] **Deliverable:** Base Laravel project

#### **TASK-004: Install Core Dependencies**
```bash
# Filament & Plugins
composer require filament/filament:"^3.0"
composer require bezhansalleh/filament-shield
composer require filament/spatie-laravel-media-library-plugin
composer require pxlrbt/filament-excel

# Laravel Core
composer require spatie/laravel-permission
composer require owen-it/laravel-auditing
composer require laravel/sanctum
composer require laravel/horizon

# Utilities
composer require kreait/laravel-firebase
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require intervention/image
composer require spatie/laravel-backup
composer require predis/predis
composer require guzzlehttp/guzzle
```
- [ ] Install all packages
- [ ] Run `php artisan vendor:publish` for configs
- [ ] Configure each package
- [ ] **Deliverable:** composer.json with all dependencies

#### **TASK-005: Configure Tailwind CSS & Vite**
- [ ] Install Tailwind CSS: `npm install -D tailwindcss postcss autoprefixer`
- [ ] Initialize Tailwind: `npx tailwindcss init -p`
- [ ] Configure `tailwind.config.js` for Filament
- [ ] Setup custom colors (Navy `#0B1A2B`, Gold `#B8893A`)
- [ ] Configure `vite.config.js`
- [ ] Test build: `npm run dev`
- [ ] **Deliverable:** Working Tailwind setup

### Week 2: Project Foundation

#### **TASK-006: Setup CI/CD Pipeline**
- [ ] Configure GitHub Actions / GitLab CI
- [ ] Create pipeline stages: test → build → deploy
- [ ] Setup automated testing on PR
- [ ] Configure deployment to staging server
- [ ] **Deliverable:** Working CI/CD pipeline

#### **TASK-007: Code Quality Tools**
- [ ] Install PHP CS Fixer: `composer require --dev friendsofphp/php-cs-fixer`
- [ ] Install PHPStan: `composer require --dev phpstan/phpstan`
- [ ] Configure `phpcs.xml` for PSR-12
- [ ] Install ESLint: `npm install --save-dev eslint`
- [ ] Configure `.eslintrc` for Airbnb style
- [ ] Add scripts to `composer.json` and `package.json`
- [ ] **Deliverable:** Linting configs

#### **TASK-008: Testing Framework Setup**
- [ ] Configure PHPUnit: `phpunit.xml`
- [ ] Setup test database
- [ ] Install Laravel Dusk for browser testing (optional)
- [ ] Create base test cases
- [ ] Write sample test to verify setup
- [ ] **Deliverable:** Working test framework

#### **TASK-009: Documentation Structure**
- [ ] Create `/docs` directory
- [ ] Setup README.md with project overview
- [ ] Document local development setup
- [ ] Document coding standards
- [ ] Create API documentation skeleton
- [ ] **Deliverable:** Initial documentation

#### **TASK-010: Project Charter & Approval**
- [ ] Finalize PRD with stakeholder feedback
- [ ] Get PRD sign-off from:
  - [ ] Ketua Bahagian Perolehan
  - [ ] Pegawai Tadbir Tertinggi (ICT)
  - [ ] Setiausaha Kerajaan Negeri
- [ ] Document project risks and mitigation
- [ ] **Deliverable:** Signed Project Charter

---

## Phase 1: Senibina & Reka Bentuk (4 minggu)

### Week 3-4: Database Design

#### **TASK-011: Database Schema Implementation**
- [ ] Review ERD.md thoroughly
- [ ] Create migration files for all 28 tables
- [ ] **Category 1: Master Data (7 tables)**
  - [ ] `migrations/2026_01_01_000001_create_jabatan_table.php`
  - [ ] `migrations/2026_01_01_000002_create_seksyen_unit_table.php`
  - [ ] `migrations/2026_01_01_000003_create_pembekal_table.php`
  - [ ] `migrations/2026_01_01_000004_create_kaedah_perolehan_table.php`
  - [ ] `migrations/2026_01_01_000005_create_kategori_skop_table.php`
  - [ ] `migrations/2026_01_01_000006_create_status_kontrak_table.php`
  - [ ] `migrations/2026_01_01_000007_create_bank_pengeluar_bon_table.php`

- [ ] **Category 2: RBAC (6 tables)**
  - [ ] `migrations/2026_01_02_000001_create_permission_tables.php` (Spatie)

- [ ] **Category 3: Users**
  - [ ] `migrations/2026_01_03_000001_create_users_table.php`
  - [ ] Add foreign keys to jabatan, seksyen_unit

- [ ] **Category 4: Core Transaction (8 tables)**
  - [ ] `migrations/2026_01_04_000001_create_daftar_sst_table.php` ⭐
  - [ ] `migrations/2026_01_04_000002_create_daftar_kontrak_table.php`
  - [ ] `migrations/2026_01_04_000003_create_bon_pelaksanaan_table.php`
  - [ ] `migrations/2026_01_04_000004_create_insurans_kontrak_table.php`
  - [ ] `migrations/2026_01_04_000005_create_lanjutan_tempoh_table.php`
  - [ ] `migrations/2026_01_04_000006_create_penilaian_prestasi_table.php`
  - [ ] `migrations/2026_01_04_000007_create_lampiran_dokumen_table.php`
  - [ ] `migrations/2026_01_04_000008_create_status_kontrak_log_table.php`

- [ ] **Category 5: Notifications (4 tables)**
  - [ ] `migrations/2026_01_05_000001_create_notifications_table.php`
  - [ ] `migrations/2026_01_05_000002_create_alert_rules_table.php`
  - [ ] `migrations/2026_01_05_000003_create_alert_history_table.php`
  - [ ] `migrations/2026_01_05_000004_create_push_subscriptions_table.php`

- [ ] **Category 6: Audit (3 tables)**
  - [ ] `migrations/2026_01_06_000001_create_audits_table.php`
  - [ ] `migrations/2026_01_06_000002_create_activity_log_table.php`
  - [ ] `migrations/2026_01_06_000003_create_login_history_table.php`

- [ ] Run migrations: `php artisan migrate`
- [ ] Verify all tables created with correct schemas
- [ ] **Deliverable:** Complete database schema

#### **TASK-012: Database Indexes**
- [ ] Add composite indexes per ERD.md
- [ ] `daftar_sst`: Alert check indexes
- [ ] `bon_pelaksanaan`: Expiry date indexes
- [ ] `daftar_kontrak`: Kategori indexes
- [ ] Test query performance with EXPLAIN
- [ ] **Deliverable:** Optimized indexes

#### **TASK-013: Database Seeders - Master Data**
- [ ] `database/seeders/JabatanSeeder.php` - SUK Kedah departments
- [ ] `database/seeders/SeksyenUnitSeeder.php` - Units within departments
- [ ] `database/seeders/KaedahPerolehanSeeder.php` - 5 procurement methods
- [ ] `database/seeders/KategoriSkopSeeder.php` - Bekalan/Perkhidmatan/Kerja
- [ ] `database/seeders/StatusKontrakSeeder.php` - Contract statuses
- [ ] `database/seeders/BankPengeluarBonSeeder.php` - Malaysian banks
- [ ] Run: `php artisan db:seed`
- [ ] **Deliverable:** Seeded master data

#### **TASK-014: Database Seeders - RBAC**
- [ ] `database/seeders/RoleSeeder.php` - 7 built-in roles
  - [ ] super-admin
  - [ ] admin
  - [ ] sk-exec
  - [ ] pengarah
  - [ ] ketua-unit
  - [ ] pic
  - [ ] audit
- [ ] `database/seeders/PermissionSeeder.php` - All permissions
  - [ ] Format: `<resource>.<action>`
  - [ ] sst.*, kontrak.*, bon.*, penilaian.*, dashboard.*, laporan.*, audit.*
- [ ] `database/seeders/RoleHasPermissionSeeder.php` - Assign permissions to roles
- [ ] **Deliverable:** RBAC structure seeded

### Week 5-6: UI/UX Design & API Design

#### **TASK-015: Filament Panel Configuration**
- [ ] Run: `php artisan filament:install --panels`
- [ ] Configure `config/filament.php`
- [ ] Set brand name: "Sistem Pengurusan Kontrak SUK Kedah"
- [ ] Configure colors: Primary (Navy), Secondary (Gold)
- [ ] Setup logo: SUK Kedah official logo
- [ ] Configure timezone: `Asia/Kuala_Lumpur`
- [ ] Set language: `ms` (Bahasa Malaysia)
- [ ] **Deliverable:** Configured Filament panel

#### **TASK-016: Translation Files**
- [ ] Create `lang/ms.json` for Malay translations
- [ ] Translate Filament default strings
- [ ] Create custom translations for:
  - [ ] Module names (Daftar SST, Daftar Kontrak, etc.)
  - [ ] Field labels
  - [ ] Validation messages
  - [ ] Alert messages
- [ ] **Deliverable:** Complete Malay translations

#### **TASK-017: Design System & Components**
- [ ] Create design system documentation
- [ ] Define color palette:
  - [ ] Primary: Navy `#0B1A2B`
  - [ ] Accent: Gold `#B8893A`
  - [ ] Success: `#10B981`, Warning: `#F59E0B`, Danger: `#DC2626`
- [ ] Typography: Inter font (Filament default)
- [ ] Icon set: Heroicons (Filament default)
- [ ] Create reusable Blade components
- [ ] **Deliverable:** Design system guide

#### **TASK-018: UI Mockups & Wireframes**
- [ ] Create wireframes for key pages:
  - [ ] Dashboard Eksekutif (executive view)
  - [ ] Dashboard PIC (daily operations)
  - [ ] Daftar SST form
  - [ ] Bon Pelaksanaan form
  - [ ] Penilaian Prestasi form
  - [ ] Laporan builder
- [ ] Get stakeholder approval on wireframes
- [ ] **Deliverable:** Approved UI mockups

#### **TASK-019: API Design Specification**
- [ ] Document REST API endpoints (OpenAPI 3.0 spec)
- [ ] Authentication: Laravel Sanctum tokens
- [ ] Endpoints:
  - [ ] `GET /api/v1/sst` - List SST
  - [ ] `POST /api/v1/sst` - Create SST
  - [ ] `GET /api/v1/sst/{id}` - Get SST details
  - [ ] `PUT /api/v1/sst/{id}` - Update SST
  - [ ] (Similar for kontrak, bon, penilaian)
  - [ ] `GET /api/v1/dashboard/stats` - Dashboard data
  - [ ] `GET /api/v1/alerts` - User alerts
- [ ] Rate limiting: 60 requests/minute
- [ ] **Deliverable:** API specification document

#### **TASK-020: Database Schema Review & Approval**
- [ ] Conduct database review session with ICT team
- [ ] Verify all business rules can be implemented
- [ ] Check foreign key constraints
- [ ] Review indexes for performance
- [ ] Get sign-off from technical lead
- [ ] **Deliverable:** Approved DDD (Database Design Document)

---

## Phase 2: Sprint 1 - Auth & RBAC (4 minggu)

### Week 7-8: Authentication Module (M1)

#### **TASK-021: User Model & Migration**
- [ ] Create User model: `php artisan make:model User`
- [ ] Add traits: `HasRoles`, `Auditable`, `SoftDeletes`
- [ ] Add fillable fields
- [ ] Add casts: `no_kad_pengenalan => encrypted`
- [ ] Add relationships:
  - [ ] `belongsTo(Jabatan)`, `belongsTo(SeksyenUnit)`
  - [ ] `hasMany(DaftarSst)`, `hasMany(PushSubscription)`
- [ ] **Deliverable:** User model

#### **TASK-022: User Registration with API EPSM**
- [ ] Create `app/Services/EPSMService.php`
- [ ] Implement `getUserDataFromEPSM(string $no_ic): ?array`
- [ ] HTTP timeout: 10 seconds, retry 3 times
- [ ] Create registration form:
  - [ ] Input: No. Kad Pengenalan
  - [ ] Auto-fill: Nama, Email, Jabatan, Jawatan from API
  - [ ] Password confirmation
- [ ] Email verification required (FR-M1-002)
- [ ] **Deliverable:** User registration with EPSM integration

#### **TASK-023: Login System**
- [ ] Filament login page customization
- [ ] Email + Password authentication
- [ ] Password strength validation (FR-M1-003):
  - [ ] Min 8 chars
  - [ ] Upper, lower, number, symbol required
- [ ] Account lockout after 5 failed attempts (FR-M1-005)
- [ ] Log all login attempts to `login_history` table
- [ ] **Deliverable:** Working login system

#### **TASK-024: Two-Factor Authentication (2FA)**
- [ ] Install Laravel Fortify: `composer require laravel/fortify`
- [ ] Enable TOTP (Time-based One-Time Password)
- [ ] 2FA setup page for users
- [ ] Require 2FA for all roles (FR-M1-004)
- [ ] QR code generation for authenticator apps
- [ ] Recovery codes generation
- [ ] **Deliverable:** 2FA system

#### **TASK-025: Password Management**
- [ ] Password reset via email (FR-M1-007)
- [ ] Reset link validity: 60 minutes
- [ ] Password change form
- [ ] Password history tracking (cannot reuse last 5)
- [ ] Force password change every 90 days (FR-M1-006)
- [ ] **Deliverable:** Complete password management

#### **TASK-026: Session Management**
- [ ] Configure session timeout: 30 minutes (FR-M1-008)
- [ ] Track last activity timestamp
- [ ] Auto-logout on timeout
- [ ] "Remember me" functionality
- [ ] Multi-device session tracking
- [ ] **Deliverable:** Session management

### Week 9-10: RBAC Implementation

#### **TASK-027: Filament Shield Installation**
- [ ] Install: `php artisan shield:install`
- [ ] Generate: `php artisan shield:generate --all`
- [ ] Verify all permissions created
- [ ] Create `ShieldSeeder.php` for initial setup
- [ ] **Deliverable:** Filament Shield configured

#### **TASK-028: Custom RBAC Policies**
- [ ] Create policies for each model
- [ ] `php artisan make:policy DaftarSstPolicy --model=DaftarSst`
- [ ] Implement methods:
  - [ ] `viewAny()`, `view()`, `create()`, `update()`, `delete()`
  - [ ] `approve()` (for high-value contracts)
- [ ] Apply row-level security (data scoping)
- [ ] Test permissions for each role
- [ ] **Deliverable:** RBAC policies

#### **TASK-029: Department & Unit Scoping**
- [ ] Create `app/Models/Scopes/DepartmentScope.php`
- [ ] Implement scoping logic per rules.md:
  - [ ] super-admin, admin, sk-exec, audit: No scoping
  - [ ] pengarah: `WHERE jabatan_kod = {user.jabatan_kod}`
  - [ ] ketua-unit: `WHERE seksyen_unit_id = {user.seksyen_unit_id}`
  - [ ] pic: `WHERE pic_id = {user.id} OR created_by = {user.id}`
- [ ] Apply scope to all relevant models
- [ ] **Deliverable:** Row-level security

#### **TASK-030: User Management Resource**
- [ ] Create Filament resource: `php artisan make:filament-resource User --generate`
- [ ] Configure form schema:
  - [ ] Name, Email, No. IC, Phone
  - [ ] Jabatan, Seksyen/Unit, Jawatan
  - [ ] Role assignment (via Filament Shield)
  - [ ] Is Active toggle
- [ ] Configure table columns:
  - [ ] Filters: Jabatan, Role, Active status
  - [ ] Actions: Edit, Deactivate, Reset password
- [ ] **Deliverable:** User management interface

#### **TASK-031: Role Management Resource**
- [ ] Create custom Filament page for role management
- [ ] List all 7 built-in roles
- [ ] Allow creating custom roles (admin only)
- [ ] Permission matrix view (checkboxes)
- [ ] Role assignment workflow:
  - [ ] Admin assigns role → Notification to Pengarah → Approval
- [ ] **Deliverable:** Role management interface

#### **TASK-032: Permission Testing**
- [ ] Write tests for each role's permissions
- [ ] Test data scoping for each role
- [ ] Test that PIC cannot see other PICs' data
- [ ] Test that Pengarah can see all department data
- [ ] Test that audit has read-only access
- [ ] **Deliverable:** Passing permission tests

#### **TASK-033: Audit Trail Integration**
- [ ] Configure `config/auditing.php`
- [ ] Add `Auditable` trait to all models
- [ ] Test audit trail captures:
  - [ ] User who performed action
  - [ ] IP address
  - [ ] User agent
  - [ ] Old values vs new values
  - [ ] Timestamp
- [ ] Create Audit Log viewer (admin only)
- [ ] **Deliverable:** Working audit trail

#### **TASK-034: Sprint 1 Demo**
- [ ] Prepare demo script
- [ ] Demonstrate:
  - [ ] User registration with EPSM
  - [ ] Login with 2FA
  - [ ] Password management
  - [ ] Role-based access control
  - [ ] Data scoping (PIC vs Pengarah views)
  - [ ] Audit trail
- [ ] Gather stakeholder feedback
- [ ] **Deliverable:** Sprint 1 demo session

---

## Phase 3: Sprint 2 - SST & Kontrak (4 minggu)

### Week 11-12: Daftar SST Module (M2)

#### **TASK-035: SST Models & Relationships**
- [ ] Create models:
  - [ ] `php artisan make:model DaftarSst`
  - [ ] `php artisan make:model DaftarKontrak`
  - [ ] `php artisan make:model LanjutanTempoh`
  - [ ] `php artisan make:model LampiranDokumen`
  - [ ] `php artisan make:model StatusKontrakLog`
- [ ] Define relationships in each model
- [ ] Add `Auditable`, `SoftDeletes` traits
- [ ] Add `DepartmentScope` global scope
- [ ] **Deliverable:** SST models

#### **TASK-036: Pembekal Service (iDaftar Integration)**
- [ ] Create `app/Services/IDaftarService.php`
- [ ] Implement `getPembekal(string $no_pendaftaran): ?Pembekal`
- [ ] 7-day caching strategy
- [ ] Fallback to stale cache on API failure
- [ ] Retry policy: 3 attempts with exponential backoff
- [ ] Circuit breaker: suspend after 10 failures
- [ ] **Deliverable:** iDaftar integration service

#### **TASK-037: SST Filament Resource - Form**
- [ ] Create: `php artisan make:filament-resource DaftarSst --generate`
- [ ] Configure form with sections:
  - [ ] **Section 1: Maklumat Asas**
    - [ ] No. Rujukan SST (unique validation)
    - [ ] Tarikh SST (date picker)
    - [ ] Jabatan (select, auto-filled from user)
    - [ ] Seksyen/Unit (dependent select)
    - [ ] PIC Projek (user select)
  - [ ] **Section 2: Maklumat Pembekal**
    - [ ] No. Pendaftaran Pembekal (with API lookup button)
    - [ ] Nama Pembekal (auto-filled from API)
    - [ ] PIC Pembekal (Nama, Telefon, Email)
  - [ ] **Section 3: Maklumat Perolehan**
    - [ ] Skop (radio: Bekalan/Perkhidmatan/Kerja)
    - [ ] Kaedah Perolehan (select)
    - [ ] Tajuk Perjanjian
    - [ ] No. Perolehan, No. LO, Tarikh LO
    - [ ] Nilai Kontrak (currency format)
    - [ ] Tempoh Kontrak (bulan)
    - [ ] Tarikh Mula, Tarikh Tamat
  - [ ] **Section 4: Maklumat Tambahan**
    - [ ] Kontrak Formal (toggle, auto-set if tempoh > 4)
    - [ ] Penalti/Denda (textarea)
    - [ ] Catatan (textarea)
- [ ] **Deliverable:** SST form

#### **TASK-038: SST Auto-fill from API**
- [ ] Implement `afterStateUpdated()` for No. Pendaftaran field
- [ ] Call `IDaftarService::getPembekal()`
- [ ] Auto-fill Nama Pembekal, Alamat, etc.
- [ ] Show loading indicator during API call
- [ ] Handle API errors gracefully
- [ ] **Deliverable:** API auto-fill functionality

#### **TASK-039: SST Form Validation**
- [ ] Implement all validation rules from rules.md:
  - [ ] `no_rujukan_sst`: unique, regex format
  - [ ] `tarikh_sst`: required, date, before_or_equal today
  - [ ] `nilai_kontrak`: required, numeric, min 0
  - [ ] `tarikh_tamat`: required, after tarikh_mula
  - [ ] `pembekal_no_daftar`: exists in pembekal table
- [ ] Custom validation messages in Malay
- [ ] **Deliverable:** Form validation

#### **TASK-040: SST Filament Resource - Table**
- [ ] Configure table columns:
  - [ ] No. Rujukan SST
  - [ ] Tarikh SST
  - [ ] Pembekal
  - [ ] Nilai Kontrak (formatted: RM 1,234,567.89)
  - [ ] Status (badge with colors)
  - [ ] Kategori Risiko (Kategori 1/2 badges)
- [ ] Configure filters:
  - [ ] Jabatan (select)
  - [ ] Status (multi-select)
  - [ ] Kategori Risiko (Kategori 1, Kategori 2)
  - [ ] Nilai Kontrak range (slider)
  - [ ] Tarikh range (date range picker)
- [ ] Configure search: No. SST, Pembekal, Tajuk
- [ ] Configure actions:
  - [ ] View details
  - [ ] Edit
  - [ ] Delete (soft delete)
- [ ] Configure bulk actions:
  - [ ] Export to Excel
  - [ ] Generate report PDF
- [ ] **Deliverable:** SST table listing

#### **TASK-041: SST Business Logic**
- [ ] Implement model observers:
  - [ ] On create: Auto-create `daftar_kontrak` record if kontrak_formal = TRUE
  - [ ] On create: Check if bon required (nilai > RM 200k)
  - [ ] On save: Auto-set kontrak_formal if tempoh > 4 months
  - [ ] On update: Log status change to `status_kontrak_log`
- [ ] **Deliverable:** SST business logic

#### **TASK-042: Lampiran Dokumen Upload**
- [ ] Add file upload field to SST form
- [ ] Configure Spatie Media Library
- [ ] Validation: PDF, JPG, PNG, max 10MB
- [ ] Store files: `storage/app/lampiran/{sst_id}/`
- [ ] File naming: `{no_sst}_{jenis}_{timestamp}.{ext}`
- [ ] Display uploaded files in table
- [ ] **Deliverable:** Document upload

### Week 13-14: Daftar Kontrak Module (M3)

#### **TASK-043: Kontrak Filament Resource**
- [ ] Create: `php artisan make:filament-resource DaftarKontrak --generate`
- [ ] Configure form:
  - [ ] SST selection (relationship)
  - [ ] Nama Kontrak (from ATS API, fallback manual)
  - [ ] Tarikh Mula/Tamat Perjanjian
  - [ ] Tarikh Deraf ke PUU
  - [ ] Tarikh Terima dari PUU
  - [ ] Tarikh Tandatangan Kontrak
  - [ ] Tarikh Stamping
  - [ ] Status Semasa (select)
  - [ ] Catatan Dalaman (textarea)
- [ ] **Deliverable:** Kontrak form

#### **TASK-044: Kontrak Tracking Workflow**
- [ ] Implement status transitions:
  - [ ] Deraf → Ke PUU → Terima dari PUU → Tandatangan → Stamping → Siap
- [ ] Visual workflow indicator (stepper component)
- [ ] Auto-calculate `is_siap` when tarikh_stamping filled
- [ ] Log each transition to `status_kontrak_log`
- [ ] **Deliverable:** Workflow tracking

#### **TASK-045: Kategori Auto-calculation**
- [ ] Create command: `php artisan make:command UpdateKategoriRisiko`
- [ ] Implement Kategori 1 detection logic:
```sql
WHERE tarikh_sst IS NOT NULL
  AND tarikh_deraf_ke_puu IS NULL
  AND DATEDIFF(tarikh_tamat, CURDATE()) <= 180
  AND kontrak_formal = TRUE
  AND status = 'aktif'
```
- [ ] Implement Kategori 2 detection logic:
```sql
WHERE tarikh_sst IS NOT NULL
  AND tarikh_deraf_ke_puu IS NULL
  AND DATEDIFF(CURDATE(), tarikh_sst) >= 120
  AND kontrak_formal = TRUE
  AND status = 'aktif'
```
- [ ] Schedule command to run daily at 8:00 AM
- [ ] **Deliverable:** Kategori auto-calculation

#### **TASK-046: Lanjutan Tempoh Resource**
- [ ] Create: `php artisan make:filament-resource LanjutanTempoh`
- [ ] Configure form:
  - [ ] SST selection
  - [ ] No. Lanjutan (1 or 2)
  - [ ] Tarikh Lanjutan Baharu
  - [ ] Tempoh Tambahan (bulan)
  - [ ] Sebab Lanjutan (min 100 chars)
  - [ ] No. Surat Lanjutan, Tarikh Surat
  - [ ] Diluluskan Oleh, Tarikh Kelulusan
- [ ] Validation: Max 2 lanjutan per SST
- [ ] **Deliverable:** Lanjutan tempoh module

#### **TASK-047: Kontrak Table & Reports**
- [ ] Configure table listing all kontrak
- [ ] Filters:
  - [ ] Status (deraf, puu, tandatangan, stamping, siap)
  - [ ] Kategori 1/2
  - [ ] Jabatan
- [ ] Highlight Kategori 1 & 2 rows (red/orange)
- [ ] Export action: Excel report
- [ ] **Deliverable:** Kontrak listing

#### **TASK-048: Sprint 2 Testing**
- [ ] Test SST CRUD operations
- [ ] Test pembekal API auto-fill
- [ ] Test file upload
- [ ] Test kontrak workflow tracking
- [ ] Test kategori auto-calculation
- [ ] Test lanjutan tempoh
- [ ] Test data scoping (PIC can only see own SST)
- [ ] **Deliverable:** Passing tests

#### **TASK-049: Sprint 2 Demo**
- [ ] Demonstrate SST registration with API auto-fill
- [ ] Show kontrak tracking workflow
- [ ] Show Kategori 1 & 2 detection
- [ ] Show document upload
- [ ] Show lanjutan tempoh
- [ ] Gather feedback
- [ ] **Deliverable:** Sprint 2 demo session

---

## Phase 4: Sprint 3 - Bon & Penilaian (4 minggu)

### Week 15-16: Bon Pelaksanaan Module (M4)

#### **TASK-050: Bon Models**
- [ ] Create models:
  - [ ] `php artisan make:model BonPelaksanaan`
  - [ ] `php artisan make:model InsuransKontrak`
- [ ] Define relationships to DaftarSst
- [ ] Add traits: `Auditable`, `SoftDeletes`
- [ ] **Deliverable:** Bon models

#### **TASK-051: Bon Pelaksanaan Resource - Form**
- [ ] Create: `php artisan make:filament-resource BonPelaksanaan --generate`
- [ ] Configure form:
  - [ ] SST selection (relationship)
  - [ ] Jenis Bon (radio: Jaminan Bank / Insurans)
  - [ ] No. Rujukan Bon
  - [ ] Nilai Bon (currency, validate <= nilai_kontrak)
  - [ ] Bank/Pengeluar (select)
  - [ ] Tarikh Mula Bon
  - [ ] Tarikh Tamat Bon
  - [ ] Status Bon (select)
  - [ ] Tarikh Serah Balik (if applicable)
  - [ ] Diserah Kepada
  - [ ] Catatan
- [ ] **Deliverable:** Bon form

#### **TASK-052: Bon Validation Rules**
- [ ] Rule 1: Bon mandatory if nilai_kontrak > RM 200,000
- [ ] Rule 2: Mutually exclusive - Bon OR Insurans
  - [ ] Check if insurans exists before allowing bon creation
- [ ] Rule 3: Tarikh tamat bon must cover tarikh tamat kontrak
  - [ ] If `tarikh_tamat_bon < tarikh_tamat_kontrak`:
    - [ ] Set `is_tarikh_valid = FALSE`
    - [ ] Show warning message
    - [ ] Trigger ALR-014 alert
- [ ] **Deliverable:** Bon validation

#### **TASK-053: Bon Status Auto-update**
- [ ] Create command: `php artisan make:command UpdateBonStatus`
- [ ] Implement status update logic:
```sql
UPDATE bon_pelaksanaan SET status_bon = CASE
    WHEN DATEDIFF(tarikh_tamat_bon, CURDATE()) <= 0 THEN 'tamat'
    WHEN DATEDIFF(tarikh_tamat_bon, CURDATE()) <= 180 THEN 'akan_tamat'
    ELSE 'aktif'
END
```
- [ ] Schedule to run daily at 8:00 AM
- [ ] **Deliverable:** Bon status auto-update

#### **TASK-054: Bon Table & Filters**
- [ ] Configure table columns:
  - [ ] No. SST
  - [ ] No. Rujukan Bon
  - [ ] Nilai Bon
  - [ ] Tarikh Tamat (with countdown badge)
  - [ ] Status (colored badge)
  - [ ] Alert flags (180/90/30/7 days)
- [ ] Filters:
  - [ ] Status (aktif, akan_tamat, tamat)
  - [ ] Jabatan
  - [ ] Bank Pengeluar
- [ ] Sort by: Tarikh Tamat (ascending - soonest first)
- [ ] **Deliverable:** Bon listing

#### **TASK-055: Bon Serah Balik Workflow**
- [ ] Add "Serah Balik Bon" action to table
- [ ] Form:
  - [ ] Tarikh Serah Balik (date picker, default today)
  - [ ] Diserah Kepada (text input)
  - [ ] Status Bon (select: serah_balik / dalam_simpanan)
  - [ ] Catatan
- [ ] Update status in database
- [ ] Log to audit trail
- [ ] **Deliverable:** Bon return workflow

#### **TASK-056: Insurans Kontrak Resource**
- [ ] Create: `php artisan make:filament-resource InsuransKontrak`
- [ ] Similar form structure to Bon
- [ ] Validation: Cannot create if bon exists
- [ ] **Deliverable:** Insurans module

### Week 17-18: Penilaian Prestasi Module (M5)

#### **TASK-057: Penilaian Prestasi Model**
- [ ] Create: `php artisan make:model PenilaianPrestasi`
- [ ] Add relationships to DaftarSst, User (diluluskan_oleh)
- [ ] Add traits: `Auditable`, `SoftDeletes`
- [ ] **Deliverable:** Penilaian model

#### **TASK-058: Penilaian Form**
- [ ] Create: `php artisan make:filament-resource PenilaianPrestasi --generate`
- [ ] Configure form with wizard steps:
  - [ ] **Step 1: Maklumat Penilaian**
    - [ ] SST selection (filtered to active contracts)
    - [ ] Bulan Penilaian (1-12)
    - [ ] Tahun Penilaian (2020-2050)
  - [ ] **Step 2: Penilaian Kriteria**
    - [ ] Skor Kualiti (slider 0-100)
    - [ ] Skor Masa (slider 0-100)
    - [ ] Skor Kos (slider 0-100)
    - [ ] Skor Keselamatan (slider 0-100)
    - [ ] Skor Perkhidmatan (slider 0-100)
  - [ ] **Step 3: Ulasan**
    - [ ] Ulasan PIC (rich text editor, min 50 chars)
    - [ ] Cadangan Penambahbaikan (textarea)
- [ ] Auto-calculate skor_purata and gred
- [ ] **Deliverable:** Penilaian form

#### **TASK-059: Penilaian Auto-calculation**
- [ ] Add model observer for PenilaianPrestasi
- [ ] On saving:
  - [ ] Calculate `skor_purata = (sum of 5 scores) / 5`
  - [ ] Assign gred:
    - [ ] A: >= 90
    - [ ] B: >= 80
    - [ ] C: >= 70
    - [ ] D: >= 60
    - [ ] E: < 60
- [ ] **Deliverable:** Auto-calculation

#### **TASK-060: Penilaian Approval Workflow**
- [ ] Status flow: deraf → hantar → lulus/tolak
- [ ] PIC can only edit in 'deraf' status
- [ ] "Submit" action: status → 'hantar', notify Ketua Unit
- [ ] Ketua Unit actions:
  - [ ] Lulus: status → 'lulus', generate PDF
  - [ ] Tolak: status → 'deraf', add ulasan_ketua, notify PIC
- [ ] **Deliverable:** Approval workflow

#### **TASK-061: Penilaian PDF Generation**
- [ ] Create PDF template: `resources/views/pdf/penilaian.blade.php`
- [ ] Include:
  - [ ] SUK Kedah logo and header
  - [ ] Contract details
  - [ ] All 5 criteria scores with bar charts
  - [ ] Average score and grade
  - [ ] PIC comments
  - [ ] Ketua Unit approval signature section
  - [ ] Footer: Generated timestamp
- [ ] Use DOMPDF to generate
- [ ] Store in: `storage/app/penilaian_pdf/{sst_id}/{year}_{month}.pdf`
- [ ] **Deliverable:** PDF generation

#### **TASK-062: Penilaian Table & Analytics**
- [ ] Configure table:
  - [ ] Columns: SST, Bulan/Tahun, Skor Purata, Gred, Status
  - [ ] Filters: Year, Month, Gred, Status
  - [ ] Sort by: Tahun, Bulan (descending - latest first)
- [ ] Highlight low scores (< 60) in red
- [ ] Add "View PDF" action
- [ ] **Deliverable:** Penilaian listing

#### **TASK-063: Supplier Performance Analytics**
- [ ] Create widget: `php artisan make:filament-widget SupplierPerformanceWidget`
- [ ] Show average scores for each supplier
- [ ] Trend chart (last 12 months)
- [ ] Ranking table (top 10 and bottom 10)
- [ ] Filter by jabatan, year
- [ ] **Deliverable:** Performance analytics

#### **TASK-064: Sprint 3 Testing**
- [ ] Test bon mandatory rule (>RM 200k)
- [ ] Test bon OR insurans mutual exclusivity
- [ ] Test bon tarikh validation
- [ ] Test bon status auto-update
- [ ] Test bon serah balik workflow
- [ ] Test penilaian form and validation
- [ ] Test penilaian approval workflow
- [ ] Test penilaian PDF generation
- [ ] Test low score detection (< 60%)
- [ ] **Deliverable:** Passing tests

#### **TASK-065: Sprint 3 Demo**
- [ ] Demonstrate bon registration and validation
- [ ] Show bon status auto-update
- [ ] Show bon expiry warnings
- [ ] Demonstrate penilaian creation and approval
- [ ] Show PDF generation
- [ ] Show supplier performance analytics
- [ ] Gather feedback
- [ ] **Deliverable:** Sprint 3 demo session

---

## Phase 5: Sprint 4 - Dashboard & Alerts (5 minggu)

### Week 19-20: Dashboard Module (M6)

#### **TASK-066: Dashboard Layout**
- [ ] Create custom Filament page: `php artisan make:filament-page Dashboard`
- [ ] Design responsive grid layout (3 columns on desktop, 1 on mobile)
- [ ] Configure navigation (make it default home page)
- [ ] **Deliverable:** Dashboard skeleton

#### **TASK-067: KPI Stats Widgets**
- [ ] Create widget: `php artisan make:filament-widget KpiStatsWidget --stats`
- [ ] Display cards:
  - [ ] Kontrak Aktif (total count)
  - [ ] Kontrak Tamat (this month)
  - [ ] Dokumen Belum Siap (Kategori 1 + 2)
  - [ ] Bon Aktif (total)
  - [ ] Bon Akan Tamat (within 90 days)
  - [ ] Bon Belum Serah (overdue)
  - [ ] Nilai Portfolio (total RM)
- [ ] Add trend indicators (up/down from last month)
- [ ] Colorize based on status (green/yellow/red)
- [ ] **Deliverable:** KPI cards

#### **TASK-068: Contract Funnel Widget**
- [ ] Create widget: `php artisan make:filament-widget ContractFunnelWidget --chart`
- [ ] Use ApexCharts funnel chart
- [ ] Stages:
  - [ ] SST Dikeluarkan
  - [ ] Deraf ke PUU
  - [ ] Terima dari PUU
  - [ ] Tandatangan
  - [ ] Stamping
  - [ ] Aktif
- [ ] Show count at each stage
- [ ] Clickable to filter table
- [ ] **Deliverable:** Funnel chart

#### **TASK-069: Contract Gantt Chart Widget**
- [ ] Create custom widget for Gantt chart
- [ ] Use JavaScript library (frappe-gantt or similar)
- [ ] Display all active contracts on timeline
- [ ] X-axis: Months (current year)
- [ ] Y-axis: Contract names
- [ ] Color code by status
- [ ] Show critical dates (PUU deadline, bon expiry)
- [ ] **Deliverable:** Gantt chart

#### **TASK-070: Risk Calendar Heatmap Widget**
- [ ] Create custom widget for calendar heatmap
- [ ] Use FullCalendar or similar
- [ ] Color intensity based on number of critical events per day
- [ ] Events:
  - [ ] Bon expiry dates
  - [ ] Contract end dates
  - [ ] PUU deadlines
  - [ ] Penilaian due dates
- [ ] Clickable dates show event details
- [ ] **Deliverable:** Calendar heatmap

#### **TASK-071: Status Donut Chart Widget**
- [ ] Create widget: `php artisan make:filament-widget StatusDonutWidget --chart`
- [ ] Use ApexCharts donut chart
- [ ] Show distribution:
  - [ ] Sihat (healthy contracts)
  - [ ] Kategori 1
  - [ ] Kategori 2
  - [ ] Bon akan tamat
  - [ ] Bon belum serah
- [ ] Clickable segments to filter
- [ ] **Deliverable:** Donut chart

#### **TASK-072: Department Heatmap Widget**
- [ ] Create widget for department performance
- [ ] Grid layout: departments x metrics
- [ ] Metrics: Aktif, Kategori 1, Kategori 2, Bon Issues
- [ ] Color coding: Green (good) → Yellow → Red (problematic)
- [ ] Hover to show exact numbers
- [ ] **Deliverable:** Department heatmap

#### **TASK-073: Trend Line Chart Widget**
- [ ] Create widget: `php artisan make:filament-widget TrendLineWidget --chart`
- [ ] Line chart showing 12-month trend:
  - [ ] SST baharu (blue line)
  - [ ] Kontrak siap (green line)
  - [ ] Kontrak tertangguh (red line)
- [ ] X-axis: Months (last 12)
- [ ] Y-axis: Count
- [ ] **Deliverable:** Trend chart

#### **TASK-074: Activity Feed Widget**
- [ ] Create widget: `php artisan make:filament-widget ActivityFeedWidget`
- [ ] Display recent activities (last 20):
  - [ ] SST registered
  - [ ] Bon registered
  - [ ] Penilaian submitted/approved
  - [ ] Status changes
  - [ ] Alerts triggered
- [ ] Show user, timestamp, description
- [ ] Link to related records
- [ ] Auto-refresh every 60 seconds
- [ ] **Deliverable:** Activity feed

#### **TASK-075: Role-based Dashboard Views**
- [ ] Configure different widget layouts per role:
  - [ ] **SK-Exec / Pengarah:** Full executive dashboard (all widgets)
  - [ ] **Ketua Unit:** Department-focused view
  - [ ] **PIC:** Personal task dashboard (my SSTs, my alerts, actions needed)
  - [ ] **Audit:** Audit-focused view (compliance metrics)
- [ ] Hide/show widgets based on permissions
- [ ] **Deliverable:** Role-based dashboards

### Week 21-22: Alert Engine (M7)

#### **TASK-076: Alert Rules Model & Seeder**
- [ ] Create model: `AlertRule`
- [ ] Seed 18 alert rules (ALR-001 to ALR-032)
- [ ] Structure per alert:
  - [ ] rule_code (e.g., ALR-001)
  - [ ] rule_name
  - [ ] trigger_type
  - [ ] trigger_days (180, 90, 30, 7, etc.)
  - [ ] recipient_roles (JSON array)
  - [ ] channels (email, inapp, push)
  - [ ] email_subject_template
  - [ ] email_body_template
  - [ ] is_active
- [ ] **Deliverable:** Alert rules seeded

#### **TASK-077: Alert Checking Command**
- [ ] Create: `php artisan make:command CheckDailyAlerts`
- [ ] Signature: `alerts:check-daily`
- [ ] Run all 18 alert checks
- [ ] For each alert:
  - [ ] Query database for matching conditions
  - [ ] For each match:
    - [ ] Create `alert_history` record
    - [ ] Dispatch notification job
    - [ ] Update tracking flags (alert_180_sent, etc.)
- [ ] Log execution results
- [ ] **Deliverable:** Alert checking command

#### **TASK-078: Alert Implementation - Kategori Kontrak (ALR-001 to ALR-004)**
- [ ] **ALR-001: Kategori 1 Baharu**
  - [ ] Query logic (see rules.md)
  - [ ] Recipients: PIC + Ketua Unit
  - [ ] Channels: Email + Push
  - [ ] Email template
- [ ] **ALR-002: Kategori 1 Berulang (7 days)**
  - [ ] Check if ALR-001 sent 7 days ago
  - [ ] Recipients: PIC + Ketua Unit + Pengarah
  - [ ] Channel: Email
- [ ] **ALR-003: Kategori 2 Baharu**
  - [ ] Query logic
  - [ ] Recipients: PIC + Ketua Unit
  - [ ] Channels: Email + Push
- [ ] **ALR-004: Kategori 2 Eskalasi (14 days)**
  - [ ] Recipients: PIC + Pengarah
  - [ ] Channel: Email
- [ ] **Deliverable:** Kategori alerts

#### **TASK-079: Alert Implementation - Bon Pelaksanaan (ALR-010 to ALR-018)**
- [ ] **ALR-010: Bon 180 hari**
  - [ ] Query: `DATEDIFF(tarikh_tamat_bon, CURDATE()) = 180`
  - [ ] Update: `alert_180_sent = TRUE`
- [ ] **ALR-011: Bon 90 hari**
  - [ ] Query: `DATEDIFF = 90`
  - [ ] Update: `alert_90_sent = TRUE`
- [ ] **ALR-012: Bon 30 hari**
  - [ ] Query: `DATEDIFF = 30`
  - [ ] Update: `alert_30_sent = TRUE`
  - [ ] Email template (see rules.md)
- [ ] **ALR-013: Bon 7 hari (KRITIKAL)**
  - [ ] Query: `DATEDIFF = 7`
  - [ ] Recipients: PIC + Ketua Unit + Pengarah
  - [ ] Priority: HIGH
  - [ ] Update: `alert_7_sent = TRUE`, `status_bon = 'akan_tamat'`
- [ ] **ALR-014: Bon Tarikh Tidak Sepadan**
  - [ ] Trigger on save (immediate)
  - [ ] Generate email to supplier
- [ ] **ALR-015: Bon Belum Serah 30 hari**
  - [ ] Query logic
- [ ] **ALR-016: Bon Belum Serah 60 hari**
  - [ ] Eskalasi to Pengarah
- [ ] **ALR-017: Bon Belum Serah 90 hari (KRITIKAL)**
  - [ ] Eskalasi to SK + Audit
- [ ] **ALR-018: Kontrak > RM200k Tiada Bon**
  - [ ] Block status change to 'siap'
- [ ] **Deliverable:** Bon alerts

#### **TASK-080: Alert Implementation - Dokumen Kontrak (ALR-020 to ALR-022)**
- [ ] **ALR-020: Stamping Tertangguh (30 hari)**
- [ ] **ALR-021: Deraf PUU Tertangguh (14 hari)**
- [ ] **ALR-022: Lanjutan Akan Tiba (30 hari)**
- [ ] **Deliverable:** Dokumen alerts

#### **TASK-081: Alert Implementation - Penilaian (ALR-030 to ALR-032)**
- [ ] **ALR-030: Peringatan Bulanan (1st of month)**
  - [ ] Run on: `DAY(CURDATE()) = 1`
  - [ ] Send to all PICs with active contracts
- [ ] **ALR-031: Penilaian Lewat (14th of month)**
  - [ ] Check missing penilaian for previous month
- [ ] **ALR-032: Prestasi Pembekal Rendah (< 60% x 2 months)**
  - [ ] Query last 2 penilaian
  - [ ] Recipients: Ketua Unit + Pengarah
- [ ] **Deliverable:** Penilaian alerts

#### **TASK-082: Scheduler Configuration**
- [ ] Configure `app/Console/Kernel.php`
- [ ] Schedule `alerts:check-daily` at 08:00 MYT
- [ ] Schedule `kategori:update-daily` at 08:00 MYT
- [ ] Schedule `bon:update-status` at 08:00 MYT
- [ ] Test scheduler: `php artisan schedule:work`
- [ ] **Deliverable:** Configured scheduler

#### **TASK-083: Alert Notification Jobs**
- [ ] Create jobs:
  - [ ] `php artisan make:job SendEmailAlertJob`
  - [ ] `php artisan make:job SendPushAlertJob`
  - [ ] `php artisan make:job SendInAppAlertJob`
- [ ] Implement each job
- [ ] Queue to Redis
- [ ] Configure retry: 3 attempts
- [ ] **Deliverable:** Notification jobs

#### **TASK-084: Email Templates**
- [ ] Create Mailable classes for each alert type
- [ ] Design email templates:
  - [ ] Use SUK Kedah branding
  - [ ] Include logo
  - [ ] Format: Plain text + HTML
  - [ ] Include {placeholders} for dynamic data
  - [ ] Include direct link to system
  - [ ] Footer: "Pemberitahuan automatik - Sistem Pengurusan Kontrak SUK Kedah"
- [ ] Test email sending (Mailtrap/Mailhog in dev)
- [ ] **Deliverable:** Email templates

### Week 23: Notification System

#### **TASK-085: In-App Notifications**
- [ ] Use Laravel Notifications (database channel)
- [ ] Display notification panel in Filament header
- [ ] Bell icon with badge count (unread)
- [ ] Dropdown showing last 10 notifications
- [ ] Click notification → mark as read → navigate to related record
- [ ] "View All" link to full notification page
- [ ] **Deliverable:** In-app notifications

#### **TASK-086: Notification Center Page**
- [ ] Create Filament page: Notification Center
- [ ] Table listing all notifications
- [ ] Filters: Read/Unread, Type, Date range
- [ ] Actions: Mark as read, Delete
- [ ] Bulk actions: Mark all as read
- [ ] **Deliverable:** Notification center

#### **TASK-087: Alert Management Interface (Admin)**
- [ ] Create Filament resource: `AlertRuleResource`
- [ ] Allow admin to:
  - [ ] View all 18 rules
  - [ ] Enable/disable rules
  - [ ] Edit email templates
  - [ ] Edit trigger_days
  - [ ] Edit recipient rules
  - [ ] Test alert (send test notification)
- [ ] **Deliverable:** Alert management

#### **TASK-088: Sprint 4 Testing**
- [ ] Test all 18 alert rules
- [ ] Verify scheduler runs at 08:00
- [ ] Test email sending
- [ ] Test in-app notifications
- [ ] Test notification center
- [ ] Test alert management
- [ ] Verify tracking flags updated
- [ ] Verify no duplicate alerts sent
- [ ] **Deliverable:** Passing alert tests

#### **TASK-089: Sprint 4 Demo**
- [ ] Demonstrate executive dashboard
- [ ] Show all widgets (Gantt, funnel, heatmap, etc.)
- [ ] Demonstrate role-based dashboard views
- [ ] Demonstrate alert engine
- [ ] Show email templates
- [ ] Show in-app notifications
- [ ] Show alert management
- [ ] Gather feedback
- [ ] **Deliverable:** Sprint 4 demo session

---

## Phase 6: PWA & Mobile (3 minggu)

### Week 24: PWA Foundation

#### **TASK-090: PWA Manifest Configuration**
- [ ] Create `public/manifest.json`
- [ ] Configure:
  - [ ] name: "Sistem Pengurusan Kontrak SUK Kedah"
  - [ ] short_name: "Kontrak SUK"
  - [ ] theme_color: "#0B1A2B" (Navy)
  - [ ] background_color: "#FFFFFF"
  - [ ] display: "standalone"
  - [ ] start_url: "/admin"
  - [ ] scope: "/"
- [ ] Generate icons (multiple sizes):
  - [ ] 192x192, 512x512 (Android)
  - [ ] 180x180, 152x152, 120x120 (iOS)
- [ ] Add `<link rel="manifest">` to head
- [ ] **Deliverable:** PWA manifest

#### **TASK-091: Service Worker Setup**
- [ ] Install Workbox: `npm install workbox-cli --save-dev`
- [ ] Create `resources/js/sw.js`
- [ ] Configure caching strategies:
  - [ ] **Cache-First:** Static assets (CSS, JS, fonts, images)
  - [ ] **Network-First:** API requests
  - [ ] **Stale-While-Revalidate:** Dashboard data
- [ ] Configure offline fallback page
- [ ] Register service worker in `resources/js/app.js`
- [ ] **Deliverable:** Service worker

#### **TASK-092: PWA Installation Prompt**
- [ ] Detect if running in standalone mode
- [ ] Show "Add to Home Screen" prompt if not installed
- [ ] Create tutorial modal for iOS (manual installation)
- [ ] Create tutorial modal for Android (auto-prompt)
- [ ] Dismiss and don't show again functionality
- [ ] **Deliverable:** Installation prompts

#### **TASK-093: Offline Mode**
- [ ] Cache last viewed data (dashboard, SST list)
- [ ] Show offline indicator when no connection
- [ ] Disable write operations when offline
- [ ] Queue actions for sync when back online
- [ ] Implement Background Sync API for penilaian drafts
- [ ] **Deliverable:** Offline functionality

### Week 25-26: Push Notifications

#### **TASK-094: Firebase Project Setup**
- [ ] Create Firebase project: "SUK Kedah Kontrak"
- [ ] Enable Firebase Cloud Messaging (FCM)
- [ ] Download `firebase-adminsdk.json` service account key
- [ ] Add to `.env`:
```
FIREBASE_PROJECT_ID=
FIREBASE_PRIVATE_KEY=
FIREBASE_CLIENT_EMAIL=
```
- [ ] Configure `config/firebase.php`
- [ ] **Deliverable:** Firebase configured

#### **TASK-095: FCM Service Worker**
- [ ] Create `public/firebase-messaging-sw.js`
- [ ] Initialize Firebase in service worker
- [ ] Handle background messages
- [ ] Handle notification click → open app to specific page
- [ ] **Deliverable:** FCM service worker

#### **TASK-096: Push Subscription Flow**
- [ ] Request notification permission on login
- [ ] Get FCM token from browser
- [ ] Save token to `push_subscriptions` table:
  - [ ] user_id
  - [ ] fcm_token
  - [ ] device_type (ios/android/web)
  - [ ] device_name
  - [ ] browser
- [ ] Handle token refresh
- [ ] **Deliverable:** Push subscription

#### **TASK-097: Push Notification Sending**
- [ ] Create `app/Services/FCMService.php`
- [ ] Implement `sendPushNotification(User $user, string $title, string $body, array $data)`
- [ ] Send to all user's active tokens
- [ ] Handle invalid/expired tokens (delete from DB)
- [ ] Retry on failure (3 attempts)
- [ ] **Deliverable:** FCM service

#### **TASK-098: Integrate Push with Alert Engine**
- [ ] Update alert jobs to send push notifications
- [ ] For ALR-001, ALR-003, etc. (where push enabled):
  - [ ] Query user's FCM tokens
  - [ ] Call `FCMService::sendPushNotification()`
  - [ ] Include deep link to related record
- [ ] **Deliverable:** Push integrated with alerts

#### **TASK-099: Push Notification Testing**
- [ ] Test on Android (Chrome)
- [ ] Test on iOS 16.4+ (Safari)
- [ ] Test notification click opens correct page
- [ ] Test notification displayed even when app closed
- [ ] Test token refresh
- [ ] Test multiple devices per user
- [ ] **Deliverable:** Working push notifications

#### **TASK-100: PWA Optimization**
- [ ] Minimize JavaScript bundle size
- [ ] Code splitting for routes
- [ ] Lazy load non-critical components
- [ ] Optimize images (WebP format)
- [ ] Enable Brotli/Gzip compression
- [ ] Target metrics:
  - [ ] First Contentful Paint < 1.8s
  - [ ] Largest Contentful Paint < 2.5s
  - [ ] Time to Interactive < 3.8s
  - [ ] Bundle size < 500KB gzipped
- [ ] **Deliverable:** Optimized PWA

#### **TASK-101: Lighthouse Audit**
- [ ] Run Lighthouse audit: `npm run lighthouse`
- [ ] Fix all issues to achieve:
  - [ ] PWA score: ≥ 90
  - [ ] Performance: ≥ 85
  - [ ] Accessibility: ≥ 90
  - [ ] Best Practices: ≥ 90
  - [ ] SEO: ≥ 80
- [ ] **Deliverable:** Lighthouse score ≥ 90

#### **TASK-102: Mobile Responsiveness Testing**
- [ ] Test on various screen sizes:
  - [ ] Mobile: 320px - 480px
  - [ ] Tablet: 768px - 1024px
  - [ ] Desktop: 1280px+
- [ ] Test on real devices:
  - [ ] iPhone (Safari)
  - [ ] Android (Chrome)
  - [ ] iPad
- [ ] Fix any UI issues
- [ ] **Deliverable:** Fully responsive app

#### **TASK-103: PWA Demo**
- [ ] Demonstrate installation on iOS
- [ ] Demonstrate installation on Android
- [ ] Show offline mode
- [ ] Show push notifications
- [ ] Show Lighthouse scores
- [ ] Test on stakeholder devices
- [ ] Gather feedback
- [ ] **Deliverable:** PWA demo session

---

## Phase 7: Integrasi (3 minggu)

### Week 27: External API Integration

#### **TASK-104: API iDaftar - Full Integration**
- [ ] Review `IDaftarService.php` from Sprint 2
- [ ] Add additional endpoints:
  - [ ] Get supplier by name (search)
  - [ ] Get supplier categories
  - [ ] Get supplier status
- [ ] Implement 7-day cache refresh
- [ ] Implement circuit breaker pattern
- [ ] Test API failure scenarios
- [ ] **Deliverable:** Complete iDaftar integration

#### **TASK-105: API EPSM - Full Integration**
- [ ] Review `EPSMService.php` from Sprint 1
- [ ] Add error handling for:
  - [ ] API timeout (10 seconds)
  - [ ] Invalid IC number
  - [ ] API down
- [ ] Add logging for all API calls
- [ ] Test with various IC numbers
- [ ] **Deliverable:** Complete EPSM integration

#### **TASK-106: API ePerolehan / ATS Integration**
- [ ] Create `app/Services/EPerolehanService.php`
- [ ] Implement methods:
  - [ ] `getPerolehanDetails(string $no_perolehan): ?array`
  - [ ] Get tajuk perjanjian, nilai kontrak, etc.
- [ ] Use in SST form auto-fill
- [ ] Handle API errors gracefully
- [ ] **Deliverable:** ePerolehan integration

#### **TASK-107: SMTP Email Configuration**
- [ ] Configure SMTP server in `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.kerajaan.kedah.gov.my
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@kedah.gov.my
MAIL_FROM_NAME="Sistem Kontrak SUK Kedah"
```
- [ ] Test email sending to stakeholders
- [ ] Configure email queue for batch sending
- [ ] **Deliverable:** Email configured

### Week 28-29: Integration Testing & API Documentation

#### **TASK-108: API Rate Limiting**
- [ ] Implement rate limiting: 60 requests/minute per user
- [ ] Use Laravel rate limiter
- [ ] Return 429 status when exceeded
- [ ] Add headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`
- [ ] **Deliverable:** Rate limiting

#### **TASK-109: REST API Development**
- [ ] Create API routes in `routes/api.php`
- [ ] Implement API controllers:
  - [ ] `Api\SstController`
  - [ ] `Api\KontrakController`
  - [ ] `Api\BonController`
  - [ ] `Api\PenilaianController`
  - [ ] `Api\DashboardController`
- [ ] Use API Resources for JSON responses
- [ ] Implement pagination
- [ ] **Deliverable:** REST API

#### **TASK-110: API Authentication (Sanctum)**
- [ ] Configure Laravel Sanctum
- [ ] Implement token-based auth
- [ ] Endpoints:
  - [ ] `POST /api/login` - Get token
  - [ ] `POST /api/logout` - Revoke token
  - [ ] `GET /api/user` - Get authenticated user
- [ ] Protect all API routes with `auth:sanctum` middleware
- [ ] **Deliverable:** API authentication

#### **TASK-111: API Documentation (OpenAPI 3.0)**
- [ ] Install Scramble: `composer require dedoc/scramble`
- [ ] Generate OpenAPI spec
- [ ] Document all endpoints:
  - [ ] Request parameters
  - [ ] Request body schemas
  - [ ] Response schemas
  - [ ] Error responses
  - [ ] Authentication
- [ ] Host documentation at `/docs/api`
- [ ] **Deliverable:** API documentation

#### **TASK-112: Integration Error Handling**
- [ ] Centralized error logging
- [ ] Monitor integration failures
- [ ] Alert admin when integration down > 30 minutes
- [ ] Implement fallback strategies:
  - [ ] Use cached data
  - [ ] Manual entry option
  - [ ] Queue for retry
- [ ] **Deliverable:** Error handling

#### **TASK-113: Integration Monitoring**
- [ ] Create dashboard widget for integration health
- [ ] Show status of each integration:
  - [ ] iDaftar (green/red)
  - [ ] EPSM (green/red)
  - [ ] ePerolehan (green/red)
  - [ ] SMTP (green/red)
  - [ ] FCM (green/red)
- [ ] Show last successful call timestamp
- [ ] Show error rate (last 24 hours)
- [ ] **Deliverable:** Integration monitoring

#### **TASK-114: End-to-End Integration Testing**
- [ ] Test full flow:
  1. User registration → EPSM API
  2. SST registration → iDaftar API for pembekal
  3. SST registration → ePerolehan API for contract details
  4. Alert triggered → Email sent via SMTP
  5. Alert triggered → Push notification via FCM
- [ ] Document any issues
- [ ] Fix integration bugs
- [ ] **Deliverable:** Integration test results

---

## Phase 8: UAT & Pembetulan (4 minggu)

### Week 30: UAT Preparation

#### **TASK-115: UAT Environment Setup**
- [ ] Provision UAT server (same specs as production)
- [ ] Deploy application to UAT
- [ ] Configure UAT database
- [ ] Seed test data (anonymized real data)
- [ ] Configure UAT domain: `uat-kontrak.kedah.gov.my`
- [ ] **Deliverable:** UAT environment

#### **TASK-116: UAT Test Data**
- [ ] Create 50 test SST records
- [ ] Create test users for each role (7 roles x 5 users = 35)
- [ ] Create test contracts with various statuses
- [ ] Create test bon records (some expiring soon)
- [ ] Create test penilaian records
- [ ] **Deliverable:** Test data

#### **TASK-117: UAT Test Cases**
- [ ] Document test cases for each module:
  - [ ] **M1 (Auth):** 15 test cases
  - [ ] **M2 (SST):** 20 test cases
  - [ ] **M3 (Kontrak):** 15 test cases
  - [ ] **M4 (Bon):** 18 test cases
  - [ ] **M5 (Penilaian):** 12 test cases
  - [ ] **M6 (Dashboard):** 10 test cases
  - [ ] **M7 (Alerts):** 18 test cases (one per alert rule)
  - [ ] **M8 (Audit):** 8 test cases
  - [ ] **M9 (Master Data):** 5 test cases
- [ ] Total: ~120 test cases
- [ ] **Deliverable:** UAT test case document

#### **TASK-118: UAT Training Materials**
- [ ] Create user manual (PDF):
  - [ ] System overview
  - [ ] User roles and permissions
  - [ ] How to register SST
  - [ ] How to track kontrak
  - [ ] How to manage bon
  - [ ] How to submit penilaian
  - [ ] How to read dashboard
  - [ ] How to respond to alerts
- [ ] Create video tutorials (screencasts)
- [ ] Create quick reference guide (cheat sheet)
- [ ] **Deliverable:** Training materials

### Week 31-33: UAT Execution & Bug Fixing

#### **TASK-119: UAT Kickoff Session**
- [ ] Schedule UAT kickoff meeting
- [ ] Distribute user accounts to testers
- [ ] Walk through test cases
- [ ] Demonstrate key features
- [ ] Answer questions
- [ ] Set UAT deadline (2 weeks)
- [ ] **Deliverable:** UAT kickoff

#### **TASK-120: UAT Monitoring & Support**
- [ ] Setup bug tracking (GitHub Issues / Jira)
- [ ] Assign severity levels:
  - [ ] Critical (blocks UAT)
  - [ ] High (major feature broken)
  - [ ] Medium (minor issue)
  - [ ] Low (cosmetic)
- [ ] Daily standup with UAT team
- [ ] Respond to questions within 4 hours
- [ ] **Deliverable:** Ongoing UAT support

#### **TASK-121: Bug Fixing - Critical**
- [ ] Fix all Critical bugs within 24 hours
- [ ] Deploy fixes to UAT immediately
- [ ] Notify testers to retest
- [ ] **Deliverable:** Critical bugs fixed

#### **TASK-122: Bug Fixing - High/Medium**
- [ ] Fix High bugs within 3 days
- [ ] Fix Medium bugs within 1 week
- [ ] Deploy fixes in batches
- [ ] **Deliverable:** High/Medium bugs fixed

#### **TASK-123: Bug Fixing - Low**
- [ ] Fix Low bugs if time permits
- [ ] Otherwise, move to backlog for post-launch
- [ ] **Deliverable:** Low bugs triaged

#### **TASK-124: UAT Feedback Incorporation**
- [ ] Review UI/UX feedback
- [ ] Make requested improvements if reasonable
- [ ] Update documentation based on feedback
- [ ] **Deliverable:** Incorporated feedback

#### **TASK-125: UAT Sign-off**
- [ ] Get sign-off from each user group:
  - [ ] SK / TSK (Eksekutif)
  - [ ] Pengarah Bahagian Perolehan
  - [ ] Ketua Unit
  - [ ] Pegawai Perolehan (PIC)
  - [ ] Pegawai Audit
  - [ ] Pentadbir Sistem
- [ ] Document any outstanding issues
- [ ] Get formal acceptance
- [ ] **Deliverable:** Signed UAT acceptance

---

## Phase 9: Migrasi Data & Latihan (3 minggu)

### Week 34: Data Migration

#### **TASK-126: Data Collection**
- [ ] Collect Excel files from all units
- [ ] Inventory of data to migrate:
  - [ ] 170+ kontrak aktif
  - [ ] Bon pelaksanaan records
  - [ ] Historical penilaian (if available)
- [ ] **Deliverable:** Collected data

#### **TASK-127: Data Cleaning**
- [ ] Review Excel data for quality
- [ ] Identify issues:
  - [ ] Missing fields
  - [ ] Invalid dates
  - [ ] Duplicate entries
  - [ ] Inconsistent formats
- [ ] Clean data manually or with scripts
- [ ] **Deliverable:** Clean data

#### **TASK-128: Data Mapping**
- [ ] Map Excel columns to database fields
- [ ] Document transformations needed
- [ ] Handle special cases:
  - [ ] Multiple lanjutan
  - [ ] Historical status changes
- [ ] **Deliverable:** Data mapping document

#### **TASK-129: Migration Script Development**
- [ ] Create `php artisan migrate:excel-data`
- [ ] Read Excel files using Maatwebsite/Excel
- [ ] Validate each row
- [ ] Import to database:
  1. Master data (jabatan, seksyen, pembekal)
  2. Users
  3. Daftar SST
  4. Daftar Kontrak
  5. Bon Pelaksanaan
  6. Historical penilaian
- [ ] Log errors and skipped rows
- [ ] **Deliverable:** Migration script

#### **TASK-130: Dry Run Migration**
- [ ] Run migration on UAT database
- [ ] Verify data integrity
- [ ] Check relationships (FKs)
- [ ] Verify calculations (kategori, skor)
- [ ] Generate report of migration results
- [ ] **Deliverable:** Dry run results

#### **TASK-131: PIC Data Verification**
- [ ] Assign each PIC to verify their own SST data
- [ ] Provide checklist:
  - [ ] No. SST correct
  - [ ] Pembekal correct
  - [ ] Nilai kontrak correct
  - [ ] Tarikh correct
  - [ ] Status correct
  - [ ] Bon details correct
- [ ] Collect feedback and corrections
- [ ] **Deliverable:** PIC verification sign-off

### Week 35-36: User Training

#### **TASK-132: Training Schedule**
- [ ] Create training schedule:
  - [ ] **Pengurusan (SK, TSK, Pengarah):** 2 sessions x 4 hours
  - [ ] **Ketua Unit:** 3 sessions x 8 hours
  - [ ] **PIC:** 8 sessions x 8 hours (different units)
  - [ ] **Pegawai Audit:** 1 session x 4 hours
  - [ ] **Pentadbir Sistem:** 1 session x 24 hours (3 days)
- [ ] Book training rooms
- [ ] Send invitations
- [ ] **Deliverable:** Training schedule

#### **TASK-133: Training Material Finalization**
- [ ] Finalize user manual
- [ ] Finalize video tutorials
- [ ] Print quick reference guides
- [ ] Prepare training environment (demo data)
- [ ] **Deliverable:** Training materials

#### **TASK-134: Eksekutif Training (4 hours)**
- [ ] Topics:
  - [ ] System overview and objectives
  - [ ] Executive dashboard walkthrough
  - [ ] How to read KPIs and charts
  - [ ] Report generation
  - [ ] How to respond to escalations
- [ ] Hands-on: Navigate dashboard, generate report
- [ ] **Deliverable:** Completed training

#### **TASK-135: Ketua Unit Training (8 hours x 3 sessions)**
- [ ] Topics:
  - [ ] System overview
  - [ ] Dashboard (unit view)
  - [ ] Monitoring unit's SST
  - [ ] Approving penilaian
  - [ ] Approving kontrak formal
  - [ ] Responding to alerts
  - [ ] Generating unit reports
- [ ] Hands-on: Create SST, approve penilaian, generate report
- [ ] **Deliverable:** Completed training

#### **TASK-136: PIC Training (8 hours x 8 sessions)**
- [ ] Topics:
  - [ ] System overview
  - [ ] User registration and login
  - [ ] Registering SST with API auto-fill
  - [ ] Uploading documents
  - [ ] Tracking kontrak workflow
  - [ ] Registering bon pelaksanaan
  - [ ] Submitting penilaian prestasi
  - [ ] Responding to alerts
  - [ ] Using PWA on mobile
- [ ] Hands-on: Full workflow from SST to penilaian
- [ ] Practice exercises
- [ ] Q&A
- [ ] **Deliverable:** Completed training (100+ PICs)

#### **TASK-137: Audit Training (4 hours)**
- [ ] Topics:
  - [ ] Read-only access overview
  - [ ] Viewing audit trail
  - [ ] Viewing activity log
  - [ ] Generating audit reports
  - [ ] Compliance checking
- [ ] Hands-on: Navigate audit features
- [ ] **Deliverable:** Completed training

#### **TASK-138: Admin Training (24 hours over 3 days)**
- [ ] Day 1: System Administration
  - [ ] User management
  - [ ] Role management
  - [ ] Master data management
  - [ ] Alert configuration
- [ ] Day 2: Technical Operations
  - [ ] Server monitoring
  - [ ] Database backups
  - [ ] Log monitoring
  - [ ] Integration health checks
  - [ ] Performance monitoring
- [ ] Day 3: Troubleshooting
  - [ ] Common issues and solutions
  - [ ] How to debug problems
  - [ ] When to escalate to vendor
  - [ ] Disaster recovery procedures
- [ ] **Deliverable:** Completed admin training

#### **TASK-139: Training Evaluation**
- [ ] Collect feedback from all training sessions
- [ ] Training evaluation form:
  - [ ] Content clarity
  - [ ] Trainer effectiveness
  - [ ] Hands-on usefulness
  - [ ] Confidence level using system
- [ ] Identify knowledge gaps
- [ ] Schedule refresher sessions if needed
- [ ] **Deliverable:** Training evaluation report

---

## Phase 10: Go-Live & Sokongan (2+ minggu)

### Week 37: Production Deployment

#### **TASK-140: Production Server Setup**
- [ ] Provision production servers per hardware specs:
  - [ ] 2x App servers (8 vCPU, 16GB RAM, 100GB SSD)
  - [ ] 1x DB Master (16 vCPU, 32GB RAM, 500GB SSD)
  - [ ] 1x DB Replica (8 vCPU, 16GB RAM, 500GB SSD)
  - [ ] 1x Redis (4 vCPU, 8GB RAM, 50GB SSD)
- [ ] Configure load balancer (Nginx)
- [ ] Configure firewall (only port 443 open)
- [ ] Setup VPN for admin access
- [ ] **Deliverable:** Production infrastructure

#### **TASK-141: SSL Certificate**
- [ ] Obtain SSL certificate for `kontrak.kedah.gov.my`
- [ ] Configure Nginx for HTTPS (TLS 1.3)
- [ ] Force HTTPS redirect
- [ ] Test SSL Labs score (A+)
- [ ] **Deliverable:** SSL configured

#### **TASK-142: Production Database Setup**
- [ ] Install MySQL 8.0 on DB server
- [ ] Configure replication (Master → Replica)
- [ ] Optimize MySQL config (`my.cnf`):
  - [ ] innodb_buffer_pool_size = 24GB (75% of RAM)
  - [ ] max_connections = 500
  - [ ] query_cache_size = 0 (disabled in MySQL 8)
- [ ] Test connection from app servers
- [ ] **Deliverable:** Production database

#### **TASK-143: Production Migration**
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed master data: `php artisan db:seed --class=MasterDataSeeder`
- [ ] Seed RBAC: `php artisan db:seed --class=RoleSeeder`
- [ ] Seed alert rules: `php artisan db:seed --class=AlertRuleSeeder`
- [ ] Verify all tables created
- [ ] **Deliverable:** Production database schema

#### **TASK-144: Production Data Migration**
- [ ] Run final data migration from Excel
- [ ] Use cleaned and verified data
- [ ] Monitor for errors
- [ ] Verify data integrity
- [ ] Generate migration report
- [ ] **Deliverable:** Migrated production data

#### **TASK-145: Production Application Deployment**
- [ ] Build assets: `npm run build`
- [ ] Deploy code to app servers
- [ ] Configure `.env` for production
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Cache configs: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] **Deliverable:** Deployed application

#### **TASK-146: Queue Workers & Scheduler**
- [ ] Setup Supervisor for queue workers
- [ ] Configure 3 queue workers on each app server
- [ ] Setup cron for scheduler: `* * * * * php artisan schedule:run`
- [ ] Test queue: dispatch test job
- [ ] Test scheduler: verify runs at 08:00
- [ ] **Deliverable:** Background jobs configured

#### **TASK-147: Monitoring Setup**
- [ ] Install Laravel Horizon: accessible at `/horizon`
- [ ] Configure application monitoring (optional: New Relic / Datadog)
- [ ] Configure uptime monitoring (Pingdom / UptimeRobot)
- [ ] Configure error tracking (Sentry / Bugsnag)
- [ ] Setup server monitoring (CPU, RAM, Disk)
- [ ] Configure alerts for critical issues
- [ ] **Deliverable:** Monitoring configured

#### **TASK-148: Backup Configuration**
- [ ] Configure automated backups using Spatie Backup
- [ ] Daily full backup at 02:00 AM
- [ ] Incremental backup every 6 hours
- [ ] Store backups on separate storage (off-site)
- [ ] Retention: 30 days daily, 90 days weekly, 365 days monthly
- [ ] Test backup restoration
- [ ] **Deliverable:** Backup system

#### **TASK-149: Security Hardening**
- [ ] Run security audit
- [ ] Disable debug mode: `APP_DEBUG=false`
- [ ] Configure CSP headers
- [ ] Configure CORS policies
- [ ] Rate limiting enabled
- [ ] SQL injection protection verified
- [ ] XSS protection verified
- [ ] CSRF protection verified
- [ ] File upload restrictions enforced
- [ ] **Deliverable:** Security audit report

#### **TASK-150: Penetration Testing**
- [ ] Hire external penetration testing service
- [ ] Test for OWASP Top 10 vulnerabilities
- [ ] Test authentication and authorization
- [ ] Test API security
- [ ] Test file upload vulnerabilities
- [ ] Generate penetration test report
- [ ] Fix all Critical and High severity issues
- [ ] **Deliverable:** Penetration test report

### Week 38+: Go-Live & Hypercare

#### **TASK-151: Go-Live Preparation**
- [ ] Final UAT sign-off verification
- [ ] All critical bugs fixed
- [ ] Performance testing passed
- [ ] Security testing passed
- [ ] Backup tested
- [ ] Disaster recovery plan documented
- [ ] Support team trained
- [ ] Go-Live checklist completed
- [ ] **Deliverable:** Go-Live readiness

#### **TASK-152: Communication Plan**
- [ ] Announce go-live date to all users
- [ ] Send reminder email 1 week before
- [ ] Send reminder email 1 day before
- [ ] Provide helpdesk contact information
- [ ] **Deliverable:** Communication sent

#### **TASK-153: Go-Live Day**
- [ ] Cutover window: Friday 6:00 PM - Sunday 6:00 PM
- [ ] Switch DNS to production: `kontrak.kedah.gov.my`
- [ ] Monitor system closely
- [ ] Support team on standby
- [ ] Send go-live confirmation email
- [ ] **Deliverable:** System LIVE ✅

#### **TASK-154: Hypercare Support (2 weeks)**
- [ ] Dedicated support team on-site at SUK
- [ ] Immediate response to issues
- [ ] Daily check-ins with key users
- [ ] Monitor system performance
- [ ] Monitor alert engine
- [ ] Monitor error logs
- [ ] Fix bugs immediately
- [ ] Document issues and resolutions
- [ ] **Deliverable:** Hypercare complete

#### **TASK-155: Post-Launch Review**
- [ ] Conduct post-launch meeting
- [ ] Review metrics:
  - [ ] User adoption rate
  - [ ] Number of SSTs registered
  - [ ] Alerts triggered and resolved
  - [ ] System uptime
  - [ ] Performance metrics
  - [ ] User feedback
- [ ] Document lessons learned
- [ ] Identify improvements for Phase 2
- [ ] **Deliverable:** Post-launch report

#### **TASK-156: 30-Day Review**
- [ ] Review system usage after 1 month
- [ ] Measure success criteria:
  - [ ] 95%+ user adoption
  - [ ] Kategori 1 & 2 reduced
  - [ ] Alert response time improved
  - [ ] Audit findings reduced
- [ ] Gather user feedback
- [ ] Plan enhancements
- [ ] **Deliverable:** 30-day review report

#### **TASK-157: Warranty Period (12 months)**
- [ ] Provide ongoing support
- [ ] SLA: Critical (2 hours), High (8 hours), Medium (24 hours), Low (1 week)
- [ ] Monthly maintenance window
- [ ] Security patches applied monthly
- [ ] Laravel version upgrades
- [ ] Quarterly performance reviews
- [ ] **Deliverable:** Ongoing support

---

## Task Summary

### Tasks by Phase

| Phase | Weeks | Tasks | Deliverables |
|---|---|---|---|
| Phase 0: Inisiasi | 2 | TASK-001 to TASK-010 | 10 tasks | Signed charter, dev environment |
| Phase 1: Senibina | 4 | TASK-011 to TASK-020 | 10 tasks | Database schema, UI mockups, API design |
| Phase 2: Sprint 1 (Auth & RBAC) | 4 | TASK-021 to TASK-034 | 14 tasks | Authentication, RBAC, audit trail |
| Phase 3: Sprint 2 (SST & Kontrak) | 4 | TASK-035 to TASK-049 | 15 tasks | SST module, Kontrak module |
| Phase 4: Sprint 3 (Bon & Penilaian) | 4 | TASK-050 to TASK-065 | 16 tasks | Bon module, Penilaian module |
| Phase 5: Sprint 4 (Dashboard & Alerts) | 5 | TASK-066 to TASK-089 | 24 tasks | Dashboard, 18 alert rules |
| Phase 6: PWA & Mobile | 3 | TASK-090 to TASK-103 | 14 tasks | PWA, push notifications |
| Phase 7: Integrasi | 3 | TASK-104 to TASK-114 | 11 tasks | API integrations, monitoring |
| Phase 8: UAT & Pembetulan | 4 | TASK-115 to TASK-125 | 11 tasks | UAT, bug fixing, sign-off |
| Phase 9: Migrasi & Latihan | 3 | TASK-126 to TASK-139 | 14 tasks | Data migration, training |
| Phase 10: Go-Live & Sokongan | 2+ | TASK-140 to TASK-157 | 18 tasks | Production deployment, support |
| **TOTAL** | **38 weeks** | **157 tasks** | **Complete system** |

---

## Critical Path Items ⚠️

These tasks are on the critical path and any delay will impact go-live:

1. **TASK-010:** Project Charter approval (blocks all development)
2. **TASK-011:** Database schema (blocks all feature development)
3. **TASK-076-081:** Alert engine implementation (18 rules, critical feature)
4. **TASK-094-099:** Push notifications (PWA requirement)
5. **TASK-125:** UAT sign-off (blocks go-live)
6. **TASK-131:** Data migration verification (blocks go-live)
7. **TASK-150:** Penetration testing (must pass before go-live)

---

## Resource Requirements

### Development Team

- **1x Project Manager** (full-time, 38 weeks)
- **2x Full-stack Laravel Developers** (full-time, 30 weeks)
- **1x Frontend Developer** (TALL stack specialist, 20 weeks)
- **1x UI/UX Designer** (part-time, 6 weeks)
- **1x QA Engineer** (full-time, 20 weeks - from Sprint 2 onwards)
- **1x DevOps Engineer** (part-time, 10 weeks)
- **1x Technical Writer** (part-time, 4 weeks for documentation)

### Client Team

- **1x Product Owner** (Pengarah Bahagian Perolehan)
- **1x Technical Lead** (Pegawai ICT)
- **5x Subject Matter Experts** (PIC, Ketua Unit for UAT)
- **1x Change Management Lead** (for training coordination)

---

## Risk Register

| Risk | Impact | Probability | Mitigation |
|---|---|---|---|
| API integrations fail | High | Medium | Build fallback manual entry, cache data |
| Stakeholder unavailable for UAT | High | Medium | Schedule UAT early, get commitments |
| Data migration issues | High | Medium | Multiple dry runs, PIC verification |
| Scope creep during development | Medium | High | Strict change control, signed PRD |
| Key developer leaves | High | Low | Knowledge sharing, documentation |
| Security vulnerability found | Critical | Low | Penetration testing, code reviews |

---

**Tamat Task Breakdown Document**

*Untuk rujukan pelaksanaan projek bersama:*
- *PRD: prd_sistem_pengurusan_kontrak_suk_kedah.md*
- *ERD: erd.md*
- *Rules: rules.md*
- *CLAUDE.md: Development guidelines*
