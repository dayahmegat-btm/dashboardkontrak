# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Sistem Pengurusan Dokumen Kontrak & Bon Pelaksanaan** - A contract and performance bond management system for Pejabat Setiausaha Kerajaan Negeri Kedah (SUK Kedah). The system manages 170+ active contracts worth over RM 18 million, tracking contract lifecycles from Surat Setuju Terima (SST/Letter of Acceptance) through performance bonds and completion.

**Primary Language:** Bahasa Malaysia (UI, documentation, database) with technical terms in English
**Technical Stack:** Laravel 11.x (PHP 8.2), FilamentPHP 3.x, MySQL 8.0, Redis 7.x, TALL Stack (Tailwind, Alpine.js, Livewire, Laravel)
**Architecture:** FilamentPHP admin panel + Progressive Web Application (PWA) with push notifications via Firebase Cloud Messaging

## Tech Stack & Versions

### Backend
- **PHP:** 8.2 LTS
- **Framework:** Laravel 11.x LTS
- **Database:** MySQL 8.0
- **Cache/Queue:** Redis 7.x
- **Web Server:** Nginx 1.24+

### Frontend
- **Admin Panel:** FilamentPHP 3.x (TALL stack: Tailwind CSS + Alpine.js + Livewire + Laravel)
- **Styling:** Tailwind CSS 3.x
- **Build Tool:** Vite 5.x
- **PWA:** Workbox 7.x (Service Worker generation)

### Key Packages
```
# Filament Core & Plugins
filament/filament              # Admin panel framework v3.x
bezhansalleh/filament-shield   # RBAC UI for spatie/permission
filament/spatie-laravel-media-library-plugin  # Media library integration
pxlrbt/filament-excel          # Excel export actions
filament/notifications         # In-app notifications UI

# Laravel Core
spatie/laravel-permission      # RBAC (7 predefined roles)
owen-it/laravel-auditing       # Audit trail for all models
laravel/sanctum                # API authentication
laravel/horizon                # Queue monitoring dashboard
kreait/laravel-firebase        # Firebase Cloud Messaging for push notifications
maatwebsite/excel              # Excel import/export
barryvdh/laravel-dompdf        # PDF report generation
spatie/laravel-medialibrary    # Document attachment management
```

## Development Commands

### Initial Setup (when code exists)
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed initial data (roles, permissions, master data)
php artisan db:seed

# Generate storage link
php artisan storage:link

# Create Filament admin user
php artisan make:filament-user

# Install Filament Shield (RBAC)
php artisan shield:install

# Generate shield policies and permissions
php artisan shield:generate --all
```

### Development Workflow
```bash
# Start local development server (Filament accessible at /admin)
php artisan serve

# Watch and compile frontend assets
npm run dev

# Run queue worker (for notifications and background jobs)
php artisan queue:work

# Run scheduler (for daily alert checks at 8:00 AM)
php artisan schedule:work

# Run Horizon (queue monitoring at /horizon)
php artisan horizon

# Access Filament admin panel
# Development: http://localhost:8000/admin
```

### Testing
```bash
# Run all tests
php artisan test

# Run tests with coverage (requires pcov/xdebug)
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests in parallel
php artisan test --parallel

# Target: 70% coverage minimum for critical modules (Auth, RBAC, Alert Engine)
```

### Code Quality
```bash
# PHP linting (PSR-12 standard)
./vendor/bin/phpcs --standard=PSR12 app/

# Fix code style automatically
./vendor/bin/phpcbf --standard=PSR12 app/

# Static analysis
./vendor/bin/phpstan analyse

# JavaScript/TypeScript linting (ESLint Airbnb)
npm run lint
```

### Database & Filament Resources
```bash
# Create new migration
php artisan make:migration create_<table_name>_table

# Create Filament resource (Model + Resource + Pages)
php artisan make:filament-resource <ModelName> --generate

# Create Filament widget (for dashboard)
php artisan make:filament-widget <WidgetName>

# Create Filament page
php artisan make:filament-page <PageName>

# Rollback last migration
php artisan migrate:rollback

# Refresh database (drop all tables and re-migrate)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Create new seeder
php artisan make:seeder <SeederName>
```

### PWA & Build
```bash
# Build for production
npm run build

# Generate Service Worker
npm run build:pwa

# Test PWA compliance (Lighthouse)
npm run lighthouse
# Target: Score >= 90

# Optimize images
npm run optimize:images
```

## System Architecture

### Core Modules (9 Functional Modules)
1. **M1: Authentication & RBAC** - Role-based access control with 7 predefined roles
2. **M2: Daftar SST / Lantikan** - Letter of Acceptance registration
3. **M3: Daftar Kontrak** - Contract document tracking (draft → PUU → signature → stamping)
4. **M4: Bon Pelaksanaan & Insurans** - Performance bond management
5. **M5: Penilaian Prestasi** - Supplier performance evaluation
6. **M6: Dashboard & Laporan** - Executive dashboard with Gantt charts, heatmaps, funnels
7. **M7: Sistem Notifikasi** - Multi-channel notification engine (email, in-app, push)
8. **M8: Audit Trail** - Complete activity logging
9. **M9: Utiliti & Master Data** - Department, section, reference code management

### 7 System Roles
- `super-admin` - Full system access (1-2 users)
- `admin` - User, role, and master data management
- `sk-exec` - Executive read-only access across all departments (SK & TSK)
- `pengarah` - Department director, full access to department contracts
- `ketua-unit` - Unit head, access to unit contracts
- `pic` - Procurement officer, registers SST, updates contracts (primary alert recipient)
- `audit` - Internal auditor, read-only access to all data and audit trail

### Critical Alert Engine
The system runs scheduled checks daily at 8:00 AM to detect and escalate:
- **Kategori 1 Contracts:** SST issued, draft not sent to PUU, contract ending in 6 months
- **Kategori 2 Contracts:** SST issued, no draft to PUU after 4 months
- **Performance Bond Expiry:** Alerts at 180, 90, 30, 7 days before expiry
- **Bond Return:** Escalating alerts 30/60/90 days after contract completion
- **Performance Evaluation:** Monthly reminders on 1st of month

Alert escalation path: PIC → Ketua Unit (Day 7) → Pengarah (Day 14) → Setiausaha Kerajaan (Day 30)

### Database Schema (28 Tables)
**Core Transaction Tables:**
- `daftar_sst` - Main contract register (SST records)
- `daftar_kontrak` - Contract document tracking
- `bon_pelaksanaan` - Performance bonds
- `insurans_kontrak` - Insurance (alternative to bonds)
- `penilaian_prestasi` - Supplier performance evaluations
- `lampiran_dokumen` - Document attachments (PDF, images)

**RBAC Tables (Spatie):**
- `users`, `roles`, `permissions`
- `model_has_roles`, `model_has_permissions`, `role_has_permissions`

**Alert & Notification Tables:**
- `alert_rules` - Configurable alert rules
- `alert_history` - Notification delivery log
- `push_subscriptions` - FCM tokens for push notifications
- `notifications` - Laravel notifications (in-app)

### Data Scoping (Row-Level Security)
All queries must respect user scope:
- `super-admin`, `admin`, `audit`, `sk-exec`: All departments
- `pengarah`: Own department only
- `ketua-unit`: Own unit/section only
- `pic`: Own registered SSTs only (unless granted additional access)

Implement via Eloquent global scopes based on `jabatan_kod` and `seksyen_unit_id`.

## Integration Points

### External APIs (HTTPS + Token Auth)
1. **API EPSM** - Employee data for user registration (`/api_kuarters.php?secret_key={KEY}&no_kp={IC}`)
2. **API iDaftar** - Supplier registration details (cache TTL: 7 days)
3. **API ePerolehan / ATS** - Procurement information (tender/quotation details)
4. **Firebase Cloud Messaging (FCM)** - Push notifications for PWA (iOS via APNS + Android)
5. **SMTP Server** - Email notifications (primary alert channel)

**Failure Handling:**
- Retry policy: 3 attempts with exponential backoff (1s, 3s, 9s)
- Circuit breaker: After 10 consecutive failures, suspend for 5 minutes
- Admin notification: Alert if integration fails > 30 minutes

## PWA Requirements

### Critical PWA Features
- **Service Worker:** Cache static assets, offline-first strategy for dashboard/lists
- **Push Notifications:** Must work on iOS 16.4+ and Android 9.0+ via FCM
- **Installation:** Support "Add to Home Screen" with custom splash screen
- **Offline Mode:** Read-only access to cached data, sync when online
- **Performance Targets:**
  - First Contentful Paint: < 1.8s
  - Largest Contentful Paint: < 2.5s
  - Lighthouse PWA score: >= 90
  - Bundle size: < 500KB gzipped

### manifest.json Requirements
- Icons: 180x180, 152x152 (iOS) and multiple Android sizes
- Display: `standalone`
- Theme color: Navy `#0B1A2B` (official institution color)
- Background color: `#FFFFFF`
- Splash screens for various device sizes

## FilamentPHP Development Guidelines

### Resource Structure
Each main entity (DaftarSst, BonPelaksanaan, etc.) should have:
- **Resource class** - Defines form schema, table columns, filters, actions
- **List page** - Table view with bulk actions, filters, search
- **Create page** - Form for new records with wizard steps for complex forms
- **Edit page** - Form for updating records
- **View page** - Read-only detailed view

### Form Builders (Filament Forms)
```php
// Example structure for DaftarSst form
Form::make([
    Section::make('Maklumat Asas')
        ->schema([
            TextInput::make('no_rujukan_sst')->required(),
            DatePicker::make('tarikh_sst')->required(),
            Select::make('jabatan_kod')->relationship('jabatan', 'nama_jabatan'),
            // Auto-fill from API EPSM/iDaftar using $get, $set
        ]),
    Section::make('Maklumat Pembekal')
        ->schema([...]),
])
```

### Table Builders (Filament Tables)
- Use **filters** for jabatan, status, tarikh ranges
- **Bulk actions** for export, status updates
- **Custom actions** for PDF generation, alert triggering
- **Column summarizers** for nilai_kontrak totals
- **Toggle columns** for quick status changes

### Dashboard Widgets
Build custom widgets for:
1. **Stats Overview Widget** - KPI cards (kontrak aktif, bon akan tamat, etc.)
2. **Chart Widgets** - Gantt chart, funnel, heatmap using ApexCharts/Chart.js
3. **Table Widget** - Recent alerts, pending actions
4. **Custom Blade Widget** - Complex visualizations (kalendar risiko)

### RBAC Integration (Filament Shield)
- Use `php artisan shield:generate` to create policies for all resources
- Override `canAccess()` in resources for row-level security (department/unit scoping)
- Apply scopes in `getEloquentQuery()` method:
```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user->hasRole('pengarah')) {
        $query->where('jabatan_kod', $user->jabatan_kod);
    } elseif ($user->hasRole('ketua-unit')) {
        $query->where('seksyen_unit_id', $user->seksyen_unit_id);
    } elseif ($user->hasRole('pic')) {
        $query->where('pic_id', $user->id);
    }

    return $query;
}
```

### Notifications (Filament Notifications)
- Use Filament's notification system for in-app alerts
- Integrate with Laravel notification channels (database, mail, FCM)
- Display notification count badge in navigation
- Make notifications clickable with ->url() to navigate to related records

### Actions & Bulk Actions
Common actions to implement:
- **Hantar ke PUU** - Mark draft sent to legal advisor
- **Serah Balik Bon** - Mark bond returned
- **Jana Laporan PDF** - Generate PDF reports
- **Eksport Excel** - Export filtered data
- **Eskalasi Alert** - Manual alert escalation

### Custom Pages
Create custom pages for:
- **Executive Dashboard** - Complex visualizations not suited for widgets
- **Alert Configuration** - Manage alert rules and templates
- **Laporan Builder** - Custom report generation with parameters
- **Kalendar Risiko** - Calendar heatmap of critical dates

### API Auto-fill Integration
Implement in form fields using `afterStateUpdated()`:
```php
TextInput::make('no_pendaftaran_pembekal')
    ->afterStateUpdated(function ($state, $set) {
        // Call iDaftar API
        $pembekal = app(IDaftarService::class)->getPembekal($state);
        $set('nama_pembekal', $pembekal->nama);
        $set('alamat_pembekal', $pembekal->alamat);
    })
    ->live(onBlur: true)
```

### Alert Engine Integration
- Schedule command runs daily at 8:00 AM
- Check all alert rules against database
- Queue notification jobs for matching records
- Log to `alert_history` table
- Send via multiple channels (email, in-app, push)
- Create Filament notifications for in-app alerts

## Business Rules

### Contract Lifecycle
1. SST issued → 2. Draft to PUU → 3. Signature → 4. Stamping → 5. Active → 6. Completion → 7. Bond return

### Critical Validations
- Contracts > RM 200,000: Performance bond **mandatory**
- Bond expiry date < Contract end date: **Automatic alert to supplier**
- Kontrak Formal: Required if contract period > 4 months
- Performance evaluation: Scores < 60% for 2 consecutive months → Escalate to Pengarah
- Supplier assessment frequency: Monthly for formal contracts

### Audit Requirements
- All CRUD operations must be logged with: user_id, IP, user_agent, timestamp, old/new values
- Soft delete only (hard delete only by super-admin after 90 days)
- Permission changes require admin approval + audit trail entry
- Login attempts (success/failure) logged to `login_history`

## Naming Conventions

### Database Tables & Columns
- Tables: `snake_case` in Malay (e.g., `daftar_sst`, `bon_pelaksanaan`)
- Columns: `snake_case` in Malay (e.g., `no_rujukan_sst`, `tarikh_tamat`)
- Foreign keys: `<table>_id` or specific like `pic_id` (refers to users.id)
- Timestamps: Always include `created_at`, `updated_at`, `deleted_at` (soft delete)

### PHP Code
- Models: PascalCase Malay (e.g., `DaftarSst`, `BonPelaksanaan`)
- Filament Resources: PascalCase + Resource suffix (e.g., `DaftarSstResource`, `BonPelaksanaanResource`)
- Widgets: PascalCase + Widget suffix (e.g., `KontrakOverviewWidget`, `AlertDashboardWidget`)
- Methods: camelCase descriptive (e.g., `semakKategoriKontrak()`, `hantarAlert()`)
- Variables: camelCase (e.g., `$tarikhTamat`, `$nilaiKontrak`)
- Follow PSR-12 coding standard

### Filament Components
- Resource names: Plural form (e.g., `DaftarSsts`, `BonPelaksanaans`)
- Form fields: snake_case matching database columns
- Table columns: snake_case matching database columns
- Navigation groups: Malay labels (e.g., 'Pengurusan Kontrak', 'Laporan')
- Page titles: Malay (e.g., 'Daftar SST', 'Penilaian Prestasi')

## Color Scheme & Branding

- **Primary:** Navy `#0B1A2B` (official institution color)
- **Accent:** Gold `#B8893A` (state government emphasis)
- **Semantic Colors:**
  - Critical/Danger: Red `#DC2626`
  - Warning: Yellow `#F59E0B`
  - Success: Green `#10B981`
  - Info: Blue `#3B82F6`
- **Typography:** Calibri/Inter (body), Serif font for executive titles
- **Logo:** SUK Kedah official logo required in header, splash screen, PDF reports

## Security & Compliance

### Security Requirements
- TLS 1.3 for all HTTPS communication
- AES-256 encryption for sensitive data at rest
- Bcrypt (12 rounds minimum) for passwords
- Password policy: 8+ chars, complexity (upper, lower, number, symbol), expire 90 days, history 5
- Two-Factor Authentication (TOTP) required for all roles
- Rate limiting: 60 requests/min per user
- CSRF protection on all POST/PUT/DELETE
- XSS protection via Blade auto-escape + CSP headers
- SQL injection protection via Eloquent ORM

### Compliance
- **Akta Perlindungan Data Peribadi 2010 (Act 709):** Clear consent, data access rights, right to delete
- **ISO/IEC 27001:2022:** ISMS compliance for Negeri Kedah
- **Audit compliance:** Address recurring audit findings on bond monitoring and contract delays

## Project Context & Goals

### Problem Statement
Current manual Excel-based tracking causes:
- 4 Kategori 1 contracts (SST issued, no draft to PUU, ending in 6 months)
- 7 Kategori 2 contracts (no draft to PUU after 4+ months)
- Missed bond expiry dates
- 3 unreturned bonds (overdue up to 128 days)
- Recurring audit findings

### Success Metrics (within 12 months of deployment)
- 80% reduction in audit findings
- 60% time savings via API auto-population
- 95% compliance on critical dates
- Zero Kategori 1 & 2 contracts within 6 months
- 100+ active users across departments

## File Organization (when code exists)

```
app/
├── Filament/
│   ├── Resources/       # Filament CRUD resources (DaftarSstResource, BonPelaksanaanResource, etc.)
│   ├── Pages/           # Custom Filament pages
│   ├── Widgets/         # Dashboard widgets (KPI cards, charts, Gantt, funnel)
│   └── RelationManagers/ # Nested resource managers
├── Models/              # Eloquent models (DaftarSst, BonPelaksanaan, etc.)
├── Policies/            # Authorization policies (integrated with Filament Shield)
├── Services/            # Business logic (AlertEngine, NotificationService)
├── Jobs/                # Queue jobs (SendAlertJob, ProcessBondExpiryJob)
└── Observers/           # Model observers for audit trail

database/
├── migrations/          # Schema migrations (28 tables)
└── seeders/             # Initial data (roles, permissions, master data)
    └── ShieldSeeder.php # Filament Shield RBAC seeder

resources/
├── views/
│   └── filament/        # Filament custom views (blade components)
└── js/                  # Alpine.js, Service Worker, FCM
    └── sw.js            # Service Worker for PWA

public/
├── manifest.json        # PWA manifest
├── icons/               # PWA icons (multiple sizes)
└── firebase-messaging-sw.js  # FCM Service Worker

config/
├── filament.php         # Filament configuration
├── permission.php       # Spatie permission config
├── auditing.php         # Audit trail config
└── firebase.php         # FCM configuration

tests/
├── Feature/             # Feature tests (critical: Alert rules, RBAC, Filament resources)
└── Unit/                # Unit tests (target: 70% coverage)
```

## Important Notes

- **No SMS:** Push notifications via FCM replace SMS for cost efficiency (RM 0.05-0.15 per SMS saved)
- **PUU, Pegawai Kewangan, Urusetia:** Not given system accounts in Phase 1; processes handled externally
- **Date format:** MySQL DATE type; UI display in Malaysian format (dd/mm/yyyy)
- **Soft delete:** All records use soft delete; hard delete only after 90 days by super-admin
- **Alert schedule:** Laravel scheduler runs alert checks at 8:00 AM daily
- **Deployment:** On-premise at Kedah state data center OR approved sovereign cloud
- **Warranty:** 12 months post go-live; 2-week hypercare period
- **Total timeline:** ~9 months from kick-off to go-live (38 weeks)

## Reference Documentation

### Core Documentation Files

**1. PRD (Product Requirements Document):** `prd_sistem_pengurusan_kontrak_suk_kedah.md`
- Complete functional requirements (FR-M1-001 through FR-M5-006)
- 18 alert rule specifications (ALR-001 through ALR-032)
- Non-functional requirements (performance, security, scalability)
- UI/UX design specifications
- API integration specifications
- Deployment architecture and hardware specs

**2. ERD (Entity Relationship Diagram):** `erd.md`
- Complete database schema for all 28 tables
- Visual ERD diagrams (Mermaid format)
- Table relationships and foreign keys
- Indexes and performance optimization
- Business rules and constraints
- Migration strategy and data retention policies

**3. Business Rules & System Logic:** `rules.md`
- Contract kategorisasi logic (Kategori 1 & 2)
- Complete alert rules engine (18 rules: ALR-001 to ALR-032)
- Field validation rules for all forms
- Workflow and approval flows
- RBAC permission matrix and enforcement
- Data scoping rules (row-level security)
- Auto-calculation logic
- Integration rules (API iDaftar, EPSM)
- Security policies (passwords, 2FA, session timeout)
- Audit trail requirements
- Data retention and archival policies

### Quick Schema Reference

When working with database:
1. Check `erd.md` for complete table schemas
2. All transaction tables use soft delete (`deleted_at`)
3. Row-level security via `jabatan_kod`, `seksyen_unit_id`, `pic_id`
4. Foreign keys use `RESTRICT` or `CASCADE` appropriately
5. Charset: `utf8mb4_unicode_ci` for Bahasa Malaysia support
