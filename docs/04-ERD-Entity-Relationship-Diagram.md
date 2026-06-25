# ERD (Entity Relationship Diagram) & Data Dictionary
## Sistem Pengurusan Barangan Makmal
### Jabatan Perkhidmatan Pembetungan Sabah (JPP)

---

## Dokumen Versi

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | - | Draf pertama |

---

## 1. Conceptual Data Model

### 1.1 Entity Overview

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        SISTEM PENGURUSAN BARANGAN MAKMAL                     │
│                                                                             │
│  ┌──────────────────────┐    ┌──────────────┐    ┌──────────────────┐      │
│  │  model_has_roles     │    │   Districts  │    │    Categories    │      │
│  │  model_has_permissions│    └──────────────┘    └──────────────────┘      │
│  └──────────┬───────────┘                                                   │
│             │                                                               │
│  ┌──────────▼──────────┐    ┌─────────────────────┐    ┌──────────────────┐ │
│  │       Users         │───<│  LoanApplications   │───<│ LoanAppItems    │ │
│  └──────────┬──────────┘    └─────────────────────┘    └───────┬──────────┘ │
│             │                                                   │           │
│  ┌──────────▼──────────┐    ┌─────────────────────┐            │           │
│  │  role_has_permissions│    │       Loans         │───<        │           │
│  └──────────────────────┘    └─────────────────────┘   ┌──────▼──────────┐ │
│                                                         │   LoanItems     │ │
│  ┌──────────────┐    ┌──────────────────┐              └──────┬──────────┘ │
│  │   Roles      │    │   Permissions    │                     │            │
│  └──────────────┘    └──────────────────┘              ┌──────▼──────────┐ │
│                                                         │     Items       │ │
│  ┌─────────────────────┐    ┌──────────────────┐       └──────┬──────────┘ │
│  │  StorageLocations   │───<│      Items       │───<  ItemConditions      │ │
│  └─────────────────────┘    └──────────────────┘       └──────────────────┘ │
│                                                                             │
│  ┌─────────────────────┐    ┌──────────────────┐                           │
│  │   Notifications     │    │   AuditLogs      │                           │
│  └─────────────────────┘    └──────────────────┘                           │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Legend:**
- `───<` One-to-Many relationship
- `───│` One-to-One relationship
- `───><` Many-to-Many relationship (via pivot table)

**Spatie Tables (shaded):** `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

---

## 2. Logical Data Model

### 2.1 Entity Relationships

```
USERS 1──M──> LOAN_APPLICATIONS
USERS 1──M──> LOANS
USERS M──1──> DISTRICTS

USERS M──M──> ROLES (via model_has_roles)
USERS M──M──> PERMISSIONS (via model_has_permissions)
ROLES M──M──> PERMISSIONS (via role_has_permissions)

LOAN_APPLICATIONS 1──M──> LOAN_APPLICATION_ITEMS
LOAN_APPLICATION_ITEMS M──1──> ITEMS

LOANS 1──M──> LOAN_ITEMS
LOAN_ITEMS M──1──> ITEMS

ITEMS M──1──> CATEGORIES
ITEMS M──1──> STORAGE_LOCATIONS
ITEMS 1──M──> ITEM_CONDITIONS

NOTIFICATIONS M──1──> USERS
AUDIT_LOGS M──1──> USERS
```

---

## 3. Physical Data Model (Table Structures)

### 3.1 Table: `roles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(50) | UNIQUE, NOT NULL | Nama role (user/admin/super_admin) |
| display_name | VARCHAR(100) | NOT NULL | Nama paparan (Pengguna/Admin/Pentadbir) |
| description | TEXT | NULL | Penerangan role |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

**Seed Data:**
| id | name | display_name |
|----|------|-------------|
| 1 | user | Pengguna |
| 2 | admin | Admin |
| 3 | super_admin | Super Admin |

---

### 3.2 Table: `districts`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(100) | NOT NULL | Nama daerah |
| code | VARCHAR(10) | UNIQUE, NOT NULL | Kod daerah (e.g. SDK, KKB) |
| address | TEXT | NULL | Alamat pejabat daerah |
| phone | VARCHAR(20) | NULL | No telefon pejabat |
| is_active | BOOLEAN | DEFAULT true | Status aktif |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

---

### 3.3 Table: `users`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(100) | NOT NULL | Nama penuh |
| email | VARCHAR(100) | UNIQUE, NOT NULL | Alamat emel (diguna untuk login) |
| password | VARCHAR(255) | NOT NULL | Kata laluan (bcrypt hash) |
| phone | VARCHAR(20) | NULL | No telefon |
| district_id | BIGINT UNSIGNED | FK -> districts.id, NULL | Daerah (null untuk HQ) |
| is_active | BOOLEAN | DEFAULT true | Status aktif |
| email_verified_at | TIMESTAMP | NULL | Tarikh verifikasi emel |
| last_login_at | TIMESTAMP | NULL | Tarikh login terakhir |
| remember_token | VARCHAR(100) | NULL | Token "remember me" |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

**Indexes:**
- UNIQUE on `email`
- INDEX on `district_id`

> **Nota:** Role dan permission untuk users diurus melalui Spatie's pivot tables (`model_has_roles`, `model_has_permissions`), bukan column `role_id`.

---

### 3.4 Table: `categories`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(100) | NOT NULL | Nama kategori |
| description | TEXT | NULL | Penerangan kategori |
| is_active | BOOLEAN | DEFAULT true | Status aktif |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

---

### 3.5 Table: `storage_locations`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(100) | NOT NULL | Nama lokasi |
| code | VARCHAR(20) | UNIQUE, NOT NULL | Kod lokasi |
| description | TEXT | NULL | Penerangan lokasi |
| is_active | BOOLEAN | DEFAULT true | Status aktif |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

---

### 3.6 Table: `items`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| name | VARCHAR(200) | NOT NULL | Nama barang |
| description | TEXT | NULL | Penerangan barang |
| quantity | INTEGER | NOT NULL, DEFAULT 0 | Jumlah/kuantiti stok |
| available_quantity | INTEGER | NOT NULL, DEFAULT 0 | Kuantiti tersedia untuk dipinjam |
| condition | ENUM('baik','rosak','service') | NOT NULL, DEFAULT 'baik' | Kondisi barang |
| status | ENUM('tersedia','dipinjam','disimpan','rosak') | NOT NULL, DEFAULT 'tersedia' | Status barang |
| category_id | BIGINT UNSIGNED | FK -> categories.id, NOT NULL | Kategori barang |
| storage_location_id | BIGINT UNSIGNED | FK -> storage_locations.id, NOT NULL | Lokasi penyimpanan |
| expiry_date | DATE | NULL | Tarikh luput (jika berkaitan) |
| image | VARCHAR(255) | NULL | Gambar barang |
| qr_code | VARCHAR(255) | NULL | QR code string/data |
| is_active | BOOLEAN | DEFAULT true | Status aktif |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

**Indexes:**
- INDEX on `category_id`
- INDEX on `storage_location_id`
- INDEX on `status`
- INDEX on `condition`

---

### 3.7 Table: `item_conditions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| item_id | BIGINT UNSIGNED | FK -> items.id, NOT NULL | Barang |
| previous_condition | ENUM('baik','rosak','service') | NOT NULL | Kondisi sebelum |
| new_condition | ENUM('baik','rosak','service') | NOT NULL | Kondisi selepas |
| notes | TEXT | NULL | Catatan perubahan |
| changed_by | BIGINT UNSIGNED | FK -> users.id, NOT NULL | Pengguna yang buat perubahan |
| created_at | TIMESTAMP | NULL | Tarikh perubahan |

---

### 3.8 Table: `loan_applications`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| application_no | VARCHAR(20) | UNIQUE, NOT NULL | No permohonan (auto: LA-YYYYMMDD-XXX) |
| user_id | BIGINT UNSIGNED | FK -> users.id, NOT NULL | Pemohon |
| district_id | BIGINT UNSIGNED | FK -> districts.id, NOT NULL | Daerah pemohon |
| start_date | DATE | NOT NULL | Tarikh mula pinjam |
| end_date | DATE | NOT NULL | Tarikh akhir pinjam |
| purpose | TEXT | NOT NULL | Tujuan pinjaman |
| status | ENUM('menunggu','diluluskan','ditolak','dibatalkan','dipinjam','dikembalikan') | NOT NULL, DEFAULT 'menunggu' | Status permohonan |
| rejection_reason | TEXT | NULL | Sebab penolakan |
| approved_by | BIGINT UNSIGNED | FK -> users.id, NULL | Pelulus |
| approved_at | TIMESTAMP | NULL | Tarikh kelulusan |
| notes | TEXT | NULL | Catatan tambahan |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

**Indexes:**
- UNIQUE on `application_no`
- INDEX on `user_id`
- INDEX on `district_id`
- INDEX on `status`
- INDEX on `approved_by`

---

### 3.9 Table: `loan_application_items`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| loan_application_id | BIGINT UNSIGNED | FK -> loan_applications.id, NOT NULL | Permohonan |
| item_id | BIGINT UNSIGNED | FK -> items.id, NOT NULL | Barang |
| quantity_requested | INTEGER | NOT NULL | Kuantiti dipohon |
| quantity_approved | INTEGER | NULL | Kuantiti diluluskan |
| created_at | TIMESTAMP | NULL | Tarikh cipta |

**Indexes:**
- UNIQUE on (`loan_application_id`, `item_id`)
- INDEX on `item_id`

---

### 3.10 Table: `loans`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| loan_no | VARCHAR(20) | UNIQUE, NOT NULL | No pinjaman (auto: LN-YYYYMMDD-XXX) |
| loan_application_id | BIGINT UNSIGNED | FK -> loan_applications.id, UNIQUE, NOT NULL | Permohonan asal |
| user_id | BIGINT UNSIGNED | FK -> users.id, NOT NULL | Peminjam |
| district_id | BIGINT UNSIGNED | FK -> districts.id, NOT NULL | Daerah peminjam |
| start_date | DATE | NOT NULL | Tarikh mula |
| end_date | DATE | NOT NULL | Tarikh dijangka pulang |
| actual_return_date | DATE | NULL | Tarikh sebenar pulang |
| status | ENUM('aktif','dipulangkan','terlewat') | NOT NULL, DEFAULT 'aktif' | Status pinjaman |
| notes | TEXT | NULL | Catatan |
| created_by | BIGINT UNSIGNED | FK -> users.id, NOT NULL | Dicipta oleh (admin) |
| created_at | TIMESTAMP | NULL | Tarikh cipta |
| updated_at | TIMESTAMP | NULL | Tarikh kemaskini |

**Indexes:**
- UNIQUE on `loan_no`
- UNIQUE on `loan_application_id`
- INDEX on `user_id`
- INDEX on `status`

---

### 3.11 Table: `loan_items`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| loan_id | BIGINT UNSIGNED | FK -> loans.id, NOT NULL | Pinjaman |
| item_id | BIGINT UNSIGNED | FK -> items.id, NOT NULL | Barang |
| quantity_loaned | INTEGER | NOT NULL | Kuantiti dipinjam |
| quantity_returned | INTEGER | DEFAULT 0 | Kuantiti dipulang |
| condition_before | ENUM('baik','rosak','service') | NOT NULL | Kondisi sebelum pinjam |
| condition_after | ENUM('baik','rosak','service') | NULL | Kondisi selepas pulang |
| returned_at | TIMESTAMP | NULL | Tarikh pulang |
| created_at | TIMESTAMP | NULL | Tarikh cipta |

**Indexes:**
- UNIQUE on (`loan_id`, `item_id`)
- INDEX on `item_id`

---

### 3.12 Table: `notifications`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| user_id | BIGINT UNSIGNED | FK -> users.id, NOT NULL | Penerima notifikasi |
| type | VARCHAR(100) | NOT NULL | Jenis notifikasi (App\Notifications\...) |
| title | VARCHAR(200) | NOT NULL | Tajuk notifikasi |
| message | TEXT | NOT NULL | Mesej notifikasi |
| data | JSON | NULL | Data tambahan |
| is_read | BOOLEAN | DEFAULT false | Status baca |
| read_at | TIMESTAMP | NULL | Tarikh baca |
| created_at | TIMESTAMP | NULL | Tarikh cipta |

**Indexes:**
- INDEX on `user_id`
- INDEX on `is_read`

---

### 3.13 Table: `audit_logs`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | ID unik |
| user_id | BIGINT UNSIGNED | FK -> users.id, NULL | Pengguna (null untuk system) |
| action | VARCHAR(50) | NOT NULL | Tindakan (create/update/delete/approve/reject) |
| entity_type | VARCHAR(100) | NOT NULL | Entiti (App\Models\Item, etc.) |
| entity_id | BIGINT UNSIGNED | NULL | ID entiti |
| old_values | JSON | NULL | Nilai lama |
| new_values | JSON | NULL | Nilai baru |
| ip_address | VARCHAR(45) | NULL | Alamat IP |
| user_agent | TEXT | NULL | Browser/device info |
| created_at | TIMESTAMP | NULL | Tarikh cipta |

**Indexes:**
- INDEX on `user_id`
- INDEX on `entity_type`
- INDEX on `action`
- INDEX on `created_at`

---

## 4. Entity Relationship Summary (Spatie Relationships)

### 4.1 Core Entity Relationships

```
districts (1) ──────< users (M)
districts (1) ──────< loan_applications (M)
districts (1) ──────< loans (M)

users (1) ──────────< loan_applications (M)
users (1) ──────────< loans (M)
users (1) ──────────< notifications (M)
users (1) ──────────< audit_logs (M)
users (1) ──────────< item_conditions (M)

categories (1) ─────< items (M)
storage_locations (1) ─< items (M)
items (1) ──────────< item_conditions (M)
items (1) ──────────< loan_application_items (M)
items (1) ──────────< loan_items (M)

loan_applications (1) ─< loan_application_items (M)
loan_applications (1) ─< loans (1)

loans (1) ──────────< loan_items (M)
```

---

## 5. SQL Migration Structure

### 5.1 Migration Order (Urutan Cipta Table)

```
# Spatie Permission Package Tables (dijana oleh vendor:publish)
1. create_permission_tables.php  (roles, permissions, model_has_roles, model_has_permissions, role_has_permissions)

# Custom Application Tables
2. create_districts_table
3. create_users_table (add district_id FK - TANPA role_id, guna Spatie)
4. create_categories_table
5. create_storage_locations_table
6. create_items_table (add category_id, storage_location_id FK)
7. create_item_conditions_table
8. create_loan_applications_table
9. create_loan_application_items_table
10. create_loans_table
11. create_loan_items_table
12. create_notifications_table
13. create_audit_logs_table
```

### 5.2 Laravel Migration Example (Items Table)

```php
Schema::create('items', function (Blueprint $table) {
    $table->id();
    $table->string('name', 200);
    $table->text('description')->nullable();
    $table->integer('quantity')->default(0);
    $table->integer('available_quantity')->default(0);
    $table->enum('condition', ['baik', 'rosak', 'service'])->default('baik');
    $table->enum('status', ['tersedia', 'dipinjam', 'disimpan', 'rosak'])->default('tersedia');
    $table->foreignId('category_id')->constrained()->onDelete('restrict');
    $table->foreignId('storage_location_id')->constrained()->onDelete('restrict');
    $table->date('expiry_date')->nullable();
    $table->string('image')->nullable();
    $table->string('qr_code')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('status');
    $table->index('condition');
});
```

---

## 6. Data Dictionary (Abridged)

| Entity | Records Expected | Growth Rate | Notes |
|--------|-----------------|-------------|-------|
| roles | 3 | Static | Tidak berubah |
| districts | ~30 | Rendah | Mengikut bilangan daerah Sabah |
| users | ~100 | Rendah | Staf JPP |
| categories | ~20 | Rendah | Kategori barang makmal |
| storage_locations | ~10 | Rendah | Lokasi di HQ |
| items | ~500 | Sederhana | Bergantung pada inventori |
| loan_applications | ~50/bulan | Sederhana | Bergantung pada operasi |
| loans | ~40/bulan | Sederhana | Selari dengan permohonan |
| notifications | ~200/bulan | Tinggi | Bergantung pada aktiviti |
| audit_logs | ~500/bulan | Tinggi | Semua transaksi direkod |
