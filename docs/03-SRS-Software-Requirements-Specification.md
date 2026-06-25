# SRS (Software Requirements Specification)
## Sistem Pengurusan Barangan Makmal
### Jabatan Perkhidmatan Pembetungan Sabah (JPP)

---

## Dokumen Versi

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | - | Draf pertama |

---

## 1. Pengenalan

### 1.1 Tujuan
Dokumen ini menyediakan spesifikasi teknikal terperinci untuk pembangunan Sistem Pengurusan Barangan Makmal JPP Sabah. Ia merangkumi keperluan fungsian, senibina sistem, rekabentuk pangkalan data, dan spesifikasi teknikal yang lain.

### 1.2 Konvensyen Dokumen
- **FR-xxx** - Functional Requirement
- **NFR-xxx** - Non-Functional Requirement
- **UC-xxx** - Use Case
- **DB-xxx** - Database Entity

---

## 2. System Architecture

### 2.1 Senibina Sistem

```
┌─────────────────────────────────────────────────┐
│                  Client Browser                   │
│         (Tailwind CSS + JavaScript Plain)         │
└─────────────────────┬───────────────────────────┘
                      │ HTTP/HTTPS
                      ▼
┌─────────────────────────────────────────────────┐
│              Laravel 13 Application               │
│  ┌──────────┐  ┌──────────┐  ┌──────────────┐  │
│  │  Routes   │  │  Blade   │  │  Controllers  │  │
│  │  web.php  │  │  Views   │  │  (MVC)       │  │
│  └──────────┘  └──────────┘  └──────┬───────┘  │
│  ┌──────────┐  ┌──────────┐         │          │
│  │  Models   │  │  Services│  ┌──────▼───────┐  │
│  │  (Eloquent)│  │  (Logic) │  │  Middleware   │  │
│  └──────────┘  └──────────┘  │  (Auth/RBAC)  │  │
│                               └──────────────┘  │
└─────────────────────┬───────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────┐
│                 MySQL Database                    │
│         (Pangkalan Data Berpusat)                 │
└─────────────────────────────────────────────────┘
```

### 2.2 Technology Stack

| Komponen | Teknologi | Justifikasi |
|----------|-----------|-------------|
| Backend Framework | Laravel 13 | MVC, Eloquent ORM, built-in auth, security features |
| Frontend | Tailwind CSS + JavaScript Plain | Ringan, cepat, tiada dependency berat |
| Database | MySQL | Reliable, widely used, sesuai untuk data berstruktur |
| Authentication | Manual (Custom) | Flexible, kawalan penuh |
| **Role & Permission** | **Spatie Laravel Permission v6+** | RBAC yang fleksibel, boleh dikonfigurasi melalui database, support cache, blade directives |
| Templating | Blade | Laravel native templating engine |
| Notifications | Laravel Notification + Mail | Built-in, mudah dikonfigurasi |
| Reporting | Laravel Excel / DomPDF | Export ke format standard |
| Version Control | Git | Standard industry |

---

## 3. Functional Hierarchy

### 3.1 Modul Sistem

```
SISTEM PENGURUSAN BARANGAN MAKMAL JPP
│
├── 1. PUBLIC MODULE
│   ├── Halaman Utama (Landing Page)
│   └── Halaman Login
│
├── 2. USER MODULE (Pegawai Daerah)
│   ├── Dashboard User
│   ├── Senarai Inventori (View Only)
│   ├── Borang Permohonan Pinjaman
│   ├── Detail Permohonan
│   ├── Profil User
│   └── Logout
│
├── 3. ADMIN MODULE (Pegawai HQ)
│   ├── Dashboard Admin
│   ├── Pengurusan Daerah (CRUD)
│   ├── Pengurusan Kategori Barang (CRUD)
│   ├── Pengurusan Inventori (CRUD)
│   ├── Pengurusan Lokasi Penyimpanan (CRUD)
│   ├── Pengurusan Pengguna (CRUD)
│   ├── Pengurusan Permohonan & Pinjaman
│   └── Laporan & Analitik
│
└── 4. ADDITIONAL MODULES
    ├── Sistem Notifikasi
    ├── QR Code Generation
    ├── Audit Trail
    ├── Approval Workflow
    └── Carian & Penapis Lanjutan
```

---

## 4. Detailed Functional Requirements

### 4.1 Modul Public

#### FR-001: Halaman Utama
| Atribut | Penerangan |
|---------|------------|
| URL | `/` |
| Method | GET |
| Controller | `PublicController@index` |
| View | `public.index` |
| Description | Landing page dengan maklumat ringkas sistem |
| Access | Public (no auth required) |

#### FR-002: Halaman Login
| Atribut | Penerangan |
|---------|------------|
| URL | `/login` |
| Method | GET, POST |
| Controller | `AuthController@login`, `AuthController@authenticate` |
| View | `auth.login` |
| Fields | Email, Password |
| Validation | Email required + valid format, Password required + min 8 chars |
| Post-Login | Redirect ke dashboard berdasarkan role |

#### FR-003: Logout
| Atribut | Penerangan |
|---------|------------|
| URL | `/logout` |
| Method | POST |
| Controller | `AuthController@logout` |
| Description | Destroy session, redirect ke halaman utama |

### 4.2 Modul User

#### FR-004: Dashboard User
| Atribut | Penerangan |
|---------|------------|
| URL | `/user/dashboard` |
| Method | GET |
| Controller | `UserDashboardController@index` |
| Middleware | `auth`, `permission:view-dashboard` |
| Content | - Ringkasan pinjaman aktif<br>- Status permohonan terkini<br>- History pinjaman (5 terkini) |

#### FR-005: Senarai Inventori
| Atribut | Penerangan |
|---------|------------|
| URL | `/user/inventory` |
| Method | GET |
| Controller | `UserInventoryController@index` |
| Middleware | `auth`, `permission:view-inventory` |
| Features | - Papar senarai barang (read-only)<br>- Carian & filter<br>- Papar kuantiti tersedia |

#### FR-006: Borang Permohonan Pinjaman
| Atribut | Penerangan |
|---------|------------|
| URL | `/user/loan-application/create` |
| Method | GET, POST |
| Controller | `UserLoanApplicationController@create`, `@store` |
| Middleware | `auth`, `permission:create-loan-application` |
| Fields | - Barang (multi-select dengan quantity)<br>- Tarikh mula & tarikh akhir<br>- Tujuan pinjaman<br>- Maklumat pemohon (auto dari profil) |
| Validation | - Barang wajib pilih minimum 1<br>- Kuantiti > 0<br>- Tarikh akhir >= tarikh mula<br>- Tujuan wajib diisi |

#### FR-007: Detail Permohonan
| Atribut | Penerangan |
|---------|------------|
| URL | `/user/loan-application/{id}` |
| Method | GET |
| Controller | `UserLoanApplicationController@show` |
| Middleware | `auth`, `permission:view-own-applications` |
| Content | - Status permohonan<br>- Senarai barang dipohon<br>- Tarikh pinjaman<br>- Tujuan<br>- Maklumat pemohon<br>- Sejarah status updates |

#### FR-008: Profil User
| Atribut | Penerangan |
|---------|------------|
| URL | `/user/profile` |
| Method | GET, PUT |
| Controller | `UserProfileController@edit`, `@update` |
| Middleware | `auth` |
| Fields | Nama, Email, No Telefon, Daerah (read-only) |

### 4.3 Modul Admin

#### FR-010: Dashboard Admin
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/dashboard` |
| Method | GET |
| Controller | `AdminDashboardController@index` |
| Middleware | `auth`, `permission:view-dashboard` |
| Content | - Total inventori<br>- Barang tersedia vs dipinjam<br>- Permohonan menunggu<br>- Permohonan diluluskan/bulan<br>- Graf trend pinjaman |

#### FR-011: Pengurusan Daerah
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/districts` |
| Method | GET, POST, PUT, DELETE |
| Controller | `DistrictController` (resource) |
| Middleware | `auth`, `permission:manage-districts` |
| Fields | Nama Daerah, Kod Daerah, Alamat, No Telefon |
| Features | CRUD lengkap, search, pagination |

#### FR-012: Pengurusan Kategori Barang
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/categories` |
| Method | GET, POST, PUT, DELETE |
| Controller | `CategoryController` (resource) |
| Middleware | `auth`, `permission:manage-categories` |
| Fields | Nama Kategori, Penerangan |
| Features | CRUD lengkap |

#### FR-013: Pengurusan Inventori
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/items` |
| Method | GET, POST, PUT, DELETE |
| Controller | `ItemController` (resource) |
| Middleware | `auth`, `permission:manage-items` |
| Fields | - Nama Barang<br>- Kuantiti<br>- Kondisi (Baik/Rosak/Service)<br>- Status (Tersedia/Dipinjam/Disimpan)<br>- Kategori (dropdown)<br>- Lokasi Penyimpanan (dropdown)<br>- Tarikh Luput (optional)<br>- Penerangan<br>- Gambar (optional) |
| Features | CRUD lengkap, search, filter, QR code generation |

#### FR-014: Pengurusan Lokasi Penyimpanan
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/storage-locations` |
| Method | GET, POST, PUT, DELETE |
| Controller | `StorageLocationController` (resource) |
| Middleware | `auth`, `permission:manage-storage-locations` |
| Fields | Nama Lokasi, Kod Lokasi, Penerangan |
| Features | CRUD lengkap |

#### FR-015: Pengurusan Pengguna
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/users` |
| Method | GET, POST, PUT, DELETE |
| Controller | `UserManagementController` (resource) |
| Middleware | `auth`, `permission:manage-users` |
| Fields | Nama, Email, Password, No Telefon, Role, Daerah |
| Features | CRUD lengkap, search, filter by role/daerah |

#### FR-016: Pengurusan Permohonan & Pinjaman
| Atribut | Penerangan |
|---------|------------|
| URL | `/admin/loan-applications` |
| Method | GET, PUT |
| Controller | `AdminLoanController@index`, `@approve`, `@reject` |
| Middleware | `auth`, `permission:manage-loan-applications` |
| Features | - Senarai semua permohonan<br>- Filter by status (Menunggu/Lulus/Tolak)<br>- Approve/Reject permohonan<br>- Lihat detail permohonan<br>- Rekod pengembalian barang |

---

## 5. Data Flow Diagrams

### 5.1 Aliran Permohonan Pinjaman

```
User                    Sistem                  Admin
  │                       │                       │
  ├─ Isi Borang ─────────>│                       │
  │                       ├─ Validasi Data        │
  │                       ├─ Simpan Permohonan    │
  │                       ├─ Status: Menunggu     │
  │                       ├─ Hantar Notifikasi ───>│
  │                       │                       ├─ Semak Permohonan
  │                       │                       ├─ Semak Stok
  │                       │                       │
  │                       │  ┌────────────────────┤
  │                       │  │ Approve?           │
  │                       │  └────────────────────┤
  │                       │                       │
  │  ┌────────────────────┤  Yes              No  │
  │  │                    │                       │
  │  │  Kurangkan Stok    │  Tolak dengan Sebab   │
  │  │  Rekod Pinjaman    │                       │
  │  │                    │                       │
  │  ├─ Notifikasi ───────>│                       │
  │  │                    ├─ Hantar Notifikasi ───>│
  │  │                    │                       │
  │  ◄─ Status Update ────│                       │
  │                       │                       │
```

---

## 6. State Transition Diagrams

### 6.1 Status Permohonan Pinjaman

```
                    ┌─────────────┐
                    │  Menunggu   │
                    │  Kelulusan  │
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
              ▼            ▼            ▼
        ┌──────────┐ ┌──────────┐ ┌──────────┐
        │ Diluluskan│ │  Ditolak │ │Dibatalkan│
        └─────┬────┘ └──────────┘ └──────────┘
              │
              ▼
        ┌──────────┐
        │ Dipinjam │
        └─────┬────┘
              │
              ▼
        ┌──────────┐
        │ Dikembali│
        │ (Selesai)│
        └──────────┘
```

### 6.2 Status Inventori Barang

```
┌───────────┐     ┌───────────┐     ┌───────────┐
│ Tersedia  │────>│ Dipinjam  │────>│ Tersedia  │
└───────────┘     └───────────┘     └───────────┘
       │                                  ▲
       │                                  │
       ▼                                  │
┌───────────┐     ┌───────────┐          │
│ Disimpan  │────>│  Rosak    │──────────┘
└───────────┘     └───────────┘
```

---

## 7. Security Requirements

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| SEC-01 | Password Hashing | Guna bcrypt (Laravel default) |
| SEC-02 | Session Management | Session timeout selepas 120 minit inactivity |
| SEC-03 | CSRF Protection | Semua POST/PUT/DELETE forms mesti ada CSRF token |
| SEC-04 | XSS Protection | Blade escaping, validate input |
| SEC-05 | SQL Injection | Eloquent ORM (parameter binding) |
| SEC-06 | RBAC | Role-based access control untuk setiap route |
| SEC-07 | Rate Limiting | Hadkan percubaan login (5 kali, lock 15 minit) |
| SEC-08 | HTTPS | Production mesti guna HTTPS |

---

## 8. Performance Requirements

| ID | Keperluan | Target |
|----|-----------|--------|
| PERF-01 | Page Load Time | < 3 saat |
| PERF-02 | Database Query | < 500ms |
| PERF-03 | Concurrent Users | 500 users |
| PERF-04 | File Upload | < 5MB per file |
| PERF-05 | Export Report | < 30 saat untuk 10,000 records |

---

## 9. Interface Requirements

### 9.1 User Interface
- Responsive design (Desktop, Tablet, Mobile)
- Bahasa Melayu
- Consistent layout dengan sidebar navigation
- Loading states untuk AJAX requests
- Toast notifications untuk feedback

### 9.2 Hardware Interfaces
- Standard web browser (Chrome, Firefox, Edge, Safari)
- Minimum screen resolution: 1024x768

### 9.3 Software Interfaces
- Email server (SMTP) untuk notifikasi
- MySQL database server
- Web server (Apache/Nginx)

---

## 10. Database Requirements

### 10.1 DBMS
- MySQL 8.0+
- Storage Engine: InnoDB
- Character Set: utf8mb4
- Collation: utf8mb4_unicode_ci

### 10.2 Backup
- Daily automated backup
- Retention: 30 hari
- Recovery Point Objective (RPO): 24 jam
- Recovery Time Objective (RTO): 4 jam
