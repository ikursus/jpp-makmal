# Technical Architecture Document
## Sistem Pengurusan Barangan Makmal
### Jabatan Perkhidmatan Pembetungan Sabah (JPP)

---

## Dokumen Versi

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | - | Draf pertama |
| 1.1 | 22 Jun 2026 | - | Tambah Spatie Laravel Permission untuk RBAC |

---

## 1. System Architecture Overview

### 1.1 Architecture Pattern: MVC (Model-View-Controller)

Sistem menggunakan pattern **Laravel MVC (Model-View-Controller)** yang memisahkan logik perniagaan, persembahan, dan data.

```
┌─────────────────────────────────────────────────────────────┐
│                     LARAVEL MVC PATTERN                      │
│                                                              │
│  REQUEST (HTTP)                                              │
│      │                                                       │
│      ▼                                                       │
│  ┌─────────────┐   ┌──────────────┐   ┌──────────────┐     │
│  │   Routes     │──>│  Controllers │──>│   Models     │     │
│  │  (web.php)   │   │  (Business   │   │  (Eloquent   │     │
│  │              │   │   Logic)     │   │   ORM)       │     │
│  └─────────────┘   └──────┬───────┘   └──────┬───────┘     │
│                           │                   │             │
│                           ▼                   ▼             │
│                    ┌──────────────┐   ┌──────────────┐     │
│                    │    Views     │   │  Database    │     │
│                    │   (Blade)    │   │   (MySQL)    │     │
│                    └──────────────┘   └──────────────┘     │
│                                                              │
│  RESPONSE (HTML/JSON)                                        │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 N-Tier Architecture

| Tier | Teknologi | Tanggungjawab |
|------|-----------|---------------|
| **Presentation Tier** | Blade + Tailwind CSS + JavaScript | Antaramuka pengguna, rendering HTML |
| **Application Tier** | Laravel 13 (Controllers, Services, Spatie) | Logik perniagaan, validasi, workflow, RBAC |
| **Data Tier** | MySQL 8.0 + Eloquent ORM | Penyimpanan dan pengambilan data |

---

## 2. Spatie Laravel Permission Integration

### 2.1 Package Installation

```bash
composer require spatie/laravel-permission

# Publish migration & config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run migration (cipta roles, permissions, dan pivot tables)
php artisan migrate
```

### 2.2 User Model Setup

```php
// app/Models/User.php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;  // Trait Spatie untuk role & permission
    
    // TIDAK perlu column role_id - roles diurus melalui pivot table model_has_roles
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'district_id', 'is_active'
    ];
    
    // Relasi ke District
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
```

### 2.3 Seeder: Roles & Permissions

```php
// database/seeders/RolePermissionSeeder.php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

public function run()
{
    // Create Permissions
    $permissions = [
        'view-dashboard',
        'view-inventory',
        'create-loan-application',
        'view-own-applications',
        'manage-districts',
        'manage-categories',
        'manage-items',
        'manage-storage-locations',
        'manage-users',
        'manage-loan-applications',
        'approve-loan-applications',
        'view-reports',
        'export-data',
    ];

    foreach ($permissions as $perm) {
        Permission::create(['name' => $perm]);
    }

    // Create Roles & Assign Permissions
    $userRole = Role::create(['name' => 'user']);
    $userRole->givePermissionTo([
        'view-dashboard', 'view-inventory', 
        'create-loan-application', 'view-own-applications'
    ]);

    $adminRole = Role::create(['name' => 'admin']);
    $adminRole->givePermissionTo([
        'view-dashboard', 'view-inventory',
        'manage-districts', 'manage-categories',
        'manage-items', 'manage-storage-locations',
        'manage-loan-applications', 'approve-loan-applications',
        'view-reports', 'export-data'
    ]);

    $superAdminRole = Role::create(['name' => 'super_admin']);
    $superAdminRole->givePermissionTo(Permission::all()); // Semua permissions
}
```

### 2.4 Blade Directives

```blade
{{-- Semak permission --}}
@can('manage-items')
    <button>Tambah Barang</button>
@endcan

{{-- Semak role --}}
@role('admin')
    <span>Panel Admin</span>
@endrole

{{-- Semak multiple roles --}}
@hasanyrole('admin|super_admin')
    <a href="/admin/dashboard">Dashboard Admin</a>
@endhasanyrole
```

---

## 3. Directory Structure (Updated with Spatie)

### 3.1 Struktur Folder Laravel

```
jpp-makmal/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── DistrictController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── ItemController.php
│   │   │   │   ├── StorageLocationController.php
│   │   │   │   ├── UserManagementController.php
│   │   │   │   └── AdminLoanController.php
│   │   │   ├── User/
│   │   │   │   ├── UserDashboardController.php
│   │   │   │   ├── UserInventoryController.php
│   │   │   │   ├── UserLoanApplicationController.php
│   │   │   │   └── UserProfileController.php
│   │   │   └── PublicController.php
│   │   ├── Middleware/
│   │   │   └── SessionTimeout.php  (Role middleware: Spatie sedia ada)
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── LoanApplicationRequest.php
│   │       ├── ItemRequest.php
│   │       └── ProfileUpdateRequest.php
│   │
│   ├── Models/
│   │   ├── User.php  (uses HasRoles trait)
│   │   ├── District.php
│   │   ├── Category.php
│   │   ├── StorageLocation.php
│   │   ├── Item.php
│   │   ├── ItemCondition.php
│   │   ├── LoanApplication.php
│   │   ├── LoanApplicationItem.php
│   │   ├── Loan.php
│   │   ├── LoanItem.php
│   │   ├── Notification.php
│   │   └── AuditLog.php
│   │
│   │  # Spatie Models (from package, not custom):
│   │  # Spatie\Permission\Models\Role
│   │  # Spatie\Permission\Models\Permission
│   │
│   ├── Services/
│   │   ├── LoanService.php
│   │   ├── InventoryService.php
│   │   ├── NotificationService.php
│   │   ├── AuditService.php
│   │   └── QRCodeService.php
│   │
│   └── Providers/
│       └── AppServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── create_permission_tables.php  ← Spatie (roles, permissions, pivot)
│   │   ├── create_districts_table.php
│   │   ├── create_users_table.php  (TANPA role_id)
│   │   ├── create_categories_table.php
│   │   ├── create_storage_locations_table.php
│   │   ├── create_items_table.php
│   │   ├── create_item_conditions_table.php
│   │   ├── create_loan_applications_table.php
│   │   ├── create_loan_application_items_table.php
│   │   ├── create_loans_table.php
│   │   ├── create_loan_items_table.php
│   │   ├── create_notifications_table.php
│   │   └── create_audit_logs_table.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php  ← Spatie roles & permissions
│       ├── DistrictSeeder.php
│       ├── CategorySeeder.php
│       ├── StorageLocationSeeder.php
│       └── UserSeeder.php
│
├── resources/views/...
├── routes/web.php
├── config/
│   └── permission.php  ← Spatie config (diterbitkan)
└── ...
```

---

## 4. Route Design (Using Spatie Permission Middleware)

### 4.1 Route Structure (web.php)

```php
// ========== PUBLIC ROUTES ==========
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========== USER ROUTES (Pegawai Daerah) ==========
Route::middleware(['auth', 'permission:view-dashboard'])
    ->prefix('user')->name('user.')->group(function () {
    
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('dashboard');
    
    Route::get('/inventory', [UserInventoryController::class, 'index'])
        ->middleware('permission:view-inventory')->name('inventory');
    
    // Loan Applications
    Route::get('/loan-applications/create', [UserLoanApplicationController::class, 'create'])
        ->middleware('permission:create-loan-application')->name('loan-applications.create');
    Route::post('/loan-applications', [UserLoanApplicationController::class, 'store'])
        ->middleware('permission:create-loan-application')->name('loan-applications.store');
    Route::get('/loan-applications/{id}', [UserLoanApplicationController::class, 'show'])
        ->middleware('permission:view-own-applications')->name('loan-applications.show');
    
    // Profile (no specific permission needed, just auth)
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
});

// ========== ADMIN ROUTES (HQ) ==========
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:view-dashboard')->name('dashboard');
    
    // CRUD Resources (menggunakan permission middleware)
    Route::resource('districts', DistrictController::class)
        ->middleware('permission:manage-districts');
    Route::resource('categories', CategoryController::class)
        ->middleware('permission:manage-categories');
    Route::resource('items', ItemController::class)
        ->middleware('permission:manage-items');
    Route::resource('storage-locations', StorageLocationController::class)
        ->middleware('permission:manage-storage-locations');
    Route::resource('users', UserManagementController::class)
        ->middleware('permission:manage-users');
    
    // Loan Management
    Route::get('/loan-applications', [AdminLoanController::class, 'index'])
        ->middleware('permission:manage-loan-applications')->name('loan-applications.index');
    Route::get('/loan-applications/{id}', [AdminLoanController::class, 'show'])
        ->middleware('permission:manage-loan-applications')->name('loan-applications.show');
    Route::put('/loan-applications/{id}/approve', [AdminLoanController::class, 'approve'])
        ->middleware('permission:approve-loan-applications')->name('loan-applications.approve');
    Route::put('/loan-applications/{id}/reject', [AdminLoanController::class, 'reject'])
        ->middleware('permission:approve-loan-applications')->name('loan-applications.reject');
    
    // Loans & Reports
    Route::get('/loans', [AdminLoanController::class, 'loans'])
        ->middleware('permission:manage-loan-applications')->name('loans.index');
    Route::get('/reports', [AdminDashboardController::class, 'reports'])
        ->middleware('permission:view-reports')->name('reports');
    Route::get('/reports/export/{type}', [AdminDashboardController::class, 'export'])
        ->middleware('permission:export-data')->name('reports.export');
});
```

---

## 5. Middleware Architecture (Spatie)

### 5.1 Authentication Middleware
- **Session-based authentication** (not token/JWT)
- Session disimpan dalam database
- Session timeout: 120 minit inactivity

### 5.2 Spatie Middleware (TIDAK perlu custom RoleMiddleware)

Spatie menyediakan middleware siap pakai. Daftar dalam `bootstrap/app.php`:

```php
// bootstrap/app.php
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => RoleMiddleware::class,
        'permission' => PermissionMiddleware::class,
        'role_or_permission' => RoleOrPermissionMiddleware::class,
    ]);
})
```

Middleware yang tersedia:
| Middleware | Fungsi | Contoh |
|-----------|--------|--------|
| `role:user` | Semak user mempunyai role tertentu | `role:admin` |
| `permission:manage-items` | Semak user mempunyai permission | `permission:manage-items` |
| `role_or_permission:admin` | Semak role ATAU permission | `role_or_permission:manage-items` |

### 5.3 Middleware Stack

```
Global Middleware:
├── CheckForMaintenanceMode
├── ValidatePostSize
├── TrimStrings
├── ConvertEmptyStringsToNull
└── TrustProxies

Web Middleware Group:
├── EncryptCookies
├── AddQueuedCookiesToResponse
├── StartSession
├── ShareErrorsFromSession
├── VerifyCsrfToken
├── SubstituteBindings
└── RecordLastActivity (custom)

Route Middleware (Spatie):
├── auth → Authenticate
├── role → Spatie\RoleMiddleware
├── permission → Spatie\PermissionMiddleware
└── session.timeout → SessionTimeout
```

---

## 6. Security Architecture

### 6.1 Security Layers

| Lapisan | Perlindungan | Implementasi |
|---------|-------------|--------------|
| Network | HTTPS | SSL Certificate |
| Application | CSRF | Laravel CSRF Token |
| Application | XSS | Blade Escaping (`{{ }}`) |
| Application | SQL Injection | Eloquent Parameter Binding |
| Application | Password | bcrypt Hashing |
| Application | Session | Encrypted + HTTP Only |
| Application | **RBAC** | **Spatie Laravel Permission** |
| Application | Rate Limiting | Laravel Rate Limiter |
| Data | Input Validation | Form Request Validation |

### 6.2 RBAC Implementation (Spatie)

```
SUPER ADMIN (role: super_admin)
├── Menerima SEMUA permissions secara automatik
├── Full access to all modules
├── User management (CRUD) - permission: manage-users
├── Can assign roles to users
└── System configuration

ADMIN (role: admin)
├── Permission: view-dashboard, view-inventory
├── Permission: manage-districts, manage-categories
├── Permission: manage-items, manage-storage-locations
├── Permission: manage-loan-applications, approve-loan-applications
├── Permission: view-reports, export-data
└── TIDAK ada permission: manage-users

USER (role: user)
├── Permission: view-dashboard, view-inventory
├── Permission: create-loan-application, view-own-applications
└── Cannot access admin modules
```

---

## 7. Services Layer

### 7.1 Service Classes

**LoanService.php**
```php
class LoanService {
    public function createApplication(array $data): LoanApplication
    public function approveApplication(LoanApplication $app, User $approver): void
    public function rejectApplication(LoanApplication $app, User $approver, string $reason): void
    public function returnItems(Loan $loan, array $returnData): void
    public function generateApplicationNo(): string
    public function generateLoanNo(): string
}
```

**InventoryService.php**
```php
class InventoryService {
    public function deductStock(Item $item, int $quantity): void
    public function restoreStock(Item $item, int $quantity): void
    public function checkAvailability(Item $item, int $quantity): bool
    public function getLowStockItems(): Collection
    public function getExpiringItems(int $days): Collection
}
```

**NotificationService.php**
```php
class NotificationService {
    public function sendApplicationSubmitted(LoanApplication $app): void
    public function sendApplicationApproved(LoanApplication $app): void
    public function sendApplicationRejected(LoanApplication $app, string $reason): void
    public function sendReturnReminder(Loan $loan): void
    public function sendLowStockAlert(Item $item): void
}
```

**AuditService.php**
```php
class AuditService {
    public function log(string $action, $entity, array $oldValues = [], array $newValues = []): void
    public function getUserActivity(int $userId): Collection
    public function getEntityHistory(string $entityType, int $entityId): Collection
}
```

---

## 8. Database Connection Configuration

### 8.1 .env Configuration (MySQL)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jpp_makmal
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 8.2 Database Tables (Spatie Included)

**Spatie Tables (auto-generated):**
1. `roles` - Senarai roles
2. `permissions` - Senarai permissions
3. `model_has_roles` - Pivot: user <-> role
4. `model_has_permissions` - Pivot: user <-> permission (direct)
5. `role_has_permissions` - Pivot: role <-> permission

**Custom Tables (13 tables):**
6. `districts` - Senarai daerah
7. `users` - Pengguna (TANPA column role_id)
8. `categories` - Kategori barang
9. `storage_locations` - Lokasi penyimpanan
10. `items` - Inventori barang
11. `item_conditions` - Rekod kondisi barang
12. `loan_applications` - Permohonan pinjaman
13. `loan_application_items` - Item dalam permohonan
14. `loans` - Pinjaman diluluskan
15. `loan_items` - Item dalam pinjaman
16. `notifications` - Notifikasi
17. `audit_logs` - Log audit

---

## 9. Frontend Structure

### 9.1 Asset Pipeline

```
resources/
├── css/
│   └── app.css (Tailwind CSS)
├── js/
│   ├── app.js (Main JavaScript)
│   ├── auth.js (Login/Register)
│   ├── loan-application.js (Borang pinjaman logic)
│   ├── dashboard.js (Chart & analytics)
│   └── utils.js (Helper functions)
└── views/
    ├── layouts/
    │   ├── app.blade.php    → Main layout (includes navbar, sidebar)
    │   ├── admin.blade.php   → Admin layout (admin sidebar)
    │   └── user.blade.php    → User layout (user sidebar)
    └── ...
```

### 9.2 Spatie Blade Directives in Views

```blade
{{-- Contoh penggunaan di sidebar admin --}}
@can('manage-items')
<a href="{{ route('admin.items.index') }}" class="nav-link">
    📦 Pengurusan Inventori
</a>
@endcan

@can('manage-users')
<a href="{{ route('admin.users.index') }}" class="nav-link">
    👥 Pengurusan Pengguna
</a>
@endcan
```

---

## 10. Development & Deployment

### 10.1 Development Environment

| Tool | Version |
|------|---------|
| PHP | 8.3+ |
| Laravel | 13.x |
| **Spatie Permission** | **v6+** |
| MySQL | 8.0+ |
| Node.js | 20.x (for build tools) |
| Composer | 2.x |
| NPM | 10.x |

### 10.2 Local Development Setup

```bash
# 1. Clone repository
git clone [repo-url] jpp-makmal
cd jpp-makmal

# 2. Install dependencies (termasuk Spatie)
composer require spatie/laravel-permission
composer install
npm install

# 3. Environment setup
cp .env.example .env
# Edit .env - set DB credentials, etc.

# 4. Generate key
php artisan key:generate

# 5. Publish Spatie config & migration
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 6. Database setup
php artisan migrate
php artisan db:seed

# 7. Build assets
npm run build

# 8. Run development server
php artisan serve
# Access at http://localhost:8000
```

### 10.3 Production Deployment

```bash
# 1. Production optimizations
php artisan optimize
php artisan route:cache
php artisan view:cache
php artisan config:cache

# 2. Build production assets
npm run build

# 3. Set production environment
# APP_ENV=production
# APP_DEBUG=false
```

---

## 11. Monitoring & Logging

### 11.1 Log Channels

| Channel | Purpose |
|---------|---------|
| `stack` | Default - single file |
| `daily` | Daily rotated logs |
| `slack` | Critical errors to Slack |

### 11.2 Key Metrics to Monitor

- Application response time
- Database query performance
- Error rate (500 errors)
- Login attempts (security)
- Loan application processing time
- API endpoint usage

---

## Appendix A: Spatie Permission Caching

```bash
# Cache permissions (untuk performance)
php artisan permission:cache-reset

# Clear cache
php artisan permission:cache-forget
```

## Appendix B: Permission Checking Examples

```php
// Dalam Controller
if ($user->can('manage-items')) {
    // user boleh manage items
}

// Dalam Blade
@can('manage-items')
    <!-- button -->
@endcan

// Dalam Middleware (route)
Route::get('/admin/items', [ItemController::class, 'index'])
    ->middleware('permission:manage-items');

// Assign role to user
$user->assignRole('admin');

// Assign permission to user directly
$user->givePermissionTo('export-data');

// Check user's roles
$user->hasRole('admin');
