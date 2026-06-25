# URS (User Requirements Specification)
## Sistem Pengurusan Barangan Makmal
### Jabatan Perkhidmatan Pembetungan Sabah (JPP)

---

## Dokumen Versi

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | - | Draf pertama |

---

## 1. Pengenalan

### 1.1 Tujuan Dokumen
Dokumen ini bertujuan untuk mendokumentasikan keperluan pengguna bagi pembangunan Sistem Pengurusan Barangan Makmal Jabatan Perkhidmatan Pembetungan Sabah (JPP). Dokumen ini akan menjadi rujukan utama dalam fasa seterusnya iaitu rekabentuk dan pembangunan sistem.

### 1.2 Skop
Sistem ini merangkumi:
- Pengurusan inventori barangan makmal di HQ
- Pengurusan permohonan pinjaman barangan dari Daerah ke HQ
- Pengurusan pinjaman yang diluluskan
- Pengurusan pengguna, daerah, kategori, dan lokasi penyimpanan
- Sistem notifikasi dan peringatan
- Pelaporan dan analitik

### 1.3 Definisi, Akronim, dan Singkatan

| Istilah | Definisi |
|---------|----------|
| JPP | Jabatan Perkhidmatan Pembetungan Sabah |
| HQ | Ibu Pejabat (Pusat) |
| Daerah | Pejabat Daerah JPP |
| User | Pengguna biasa (pegawai daerah) |
| Admin | Pentadbir sistem (pegawai HQ) |
| Inventori | Senarai barangan makmal |
| Pinjaman | Proses peminjaman barang dari HQ ke Daerah |

---

## 2. Stakeholders

| Stakeholder | Peranan | Keperluan Utama |
|-------------|---------|-----------------|
| Pegawai Daerah (User) | Memohon pinjaman barang | Borang permohonan mudah, status real-time |
| Pegawai HQ (Admin) | Melulus/mengurus pinjaman | Dashboard komprehensif, pengurusan inventori |
| Pentadbir Sistem | Menyelenggara sistem | Pengurusan pengguna, konfigurasi sistem |
| Pengurusan Atasan | Memantau operasi | Laporan dan analitik |

---

## 3. User Roles & Personas

### 3.1 Peranan Sistem (Spatie Laravel Permission)

Sistem menggunakan **Spatie Laravel Permission** untuk pengurusan role dan permission secara fleksibel. Role dan permission dikonfigurasi melalui database, membolehkan pengubahan tanpa mengubah kod.

#### Roles (Peranan)

| Role | Kod (Guard) | Penerangan |
|------|-------------|------------|
| Pengguna (User) | web | Pegawai Daerah yang memohon pinjaman |
| Admin | web | Pegawai HQ yang mengurus sistem |
| Super Admin | web | Pentadbir sistem dengan akses penuh (menerima semua permission automatik) |

#### Permissions (Kebenaran)

| Permission | Penerangan | Diberi Kepada |
|------------|-----------|---------------|
| `view-dashboard` | Akses dashboard | User, Admin, Super Admin |
| `view-inventory` | Lihat senarai inventori | User, Admin, Super Admin |
| `create-loan-application` | Buat permohonan pinjaman | User |
| `view-own-applications` | Lihat permohonan sendiri | User |
| `manage-districts` | Urus daerah (CRUD) | Admin, Super Admin |
| `manage-categories` | Urus kategori (CRUD) | Admin, Super Admin |
| `manage-items` | Urus inventori (CRUD) | Admin, Super Admin |
| `manage-storage-locations` | Urus lokasi penyimpanan | Admin, Super Admin |
| `manage-users` | Urus pengguna (CRUD) | Super Admin |
| `manage-loan-applications` | Urus permohonan & pinjaman | Admin, Super Admin |
| `approve-loan-applications` | Lulus/tolak permohonan | Admin, Super Admin |
| `view-reports` | Akses laporan & analitik | Admin, Super Admin |
| `export-data` | Eksport data ke PDF/Excel | Admin, Super Admin |

### 3.2 User Personas

**Persona 1: Pegawai Daerah (User)**
- Nama: Ahmad bin Ismail
- Jawatan: Penolong Juruteknik, Pejabat Daerah JPP Sandakan
- Tahap IT: Sederhana
- Keperluan: Memohon pinjaman alat makmal dengan cepat, semak status permohonan

**Persona 2: Pegawai HQ (Admin)**
- Nama: Siti binti Abdullah
- Jawatan: Penolong Pegawai Tadbir, HQ JPP Kota Kinabalu
- Tahap IT: Mahir
- Keperluan: Mengurus inventori, melulus/menolak permohonan, menyediakan laporan

**Persona 3: Pentadbir Sistem (Super Admin)**
- Nama: Mohd Faiz bin Razak
- Jawatan: Pegawai IT, HQ JPP
- Tahap IT: Sangat Mahir
- Keperluan: Mengurus pengguna, tetapan sistem, penyelenggaraan

---

## 4. Functional Requirements

### 4.1 Modul Public Page

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| FR-001 | Halaman Utama | Sistem mesti memaparkan halaman utama dengan maklumat ringkas sistem |
| FR-002 | Halaman Login | Sistem mesti menyediakan halaman login dengan medan emel dan kata laluan |
| FR-003 | Autentikasi | Sistem mesti mengesahkan login pengguna dan mengarahkan ke dashboard mengikut peranan |

### 4.2 Modul User (Logged-in Area)

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| FR-004 | Dashboard User | Sistem mesti memaparkan dashboard ringkasan inventori yang dipinjam oleh user |
| FR-005 | Senarai Inventori | Sistem mesti memaparkan senarai inventori barang makmal di HQ |
| FR-006 | Borang Permohonan | Sistem mesti menyediakan borang permohonan pinjaman dengan: |
| FR-006a | - Pemilihan Barang | Multi-select barang yang ingin dipinjam termasuk jumlah/kuantiti |
| FR-006b | - Tarikh Pinjaman | Tarikh pinjam dari dan hingga |
| FR-006c | - Tujuan Pinjaman | Medan untuk tujuan pinjaman |
| FR-006d | - Maklumat Pemohon | Maklumat pemohon termasuk daerah (auto-populate dari profil) |
| FR-007 | Detail Permohonan | Sistem mesti memaparkan detail sesuatu permohonan |
| FR-008 | Profil User | Sistem mesti membenarkan user melihat dan mengemaskini profil |
| FR-009 | Logout | Sistem mesti membenarkan user logout |

### 4.3 Modul Admin

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| FR-010 | Dashboard Admin | Sistem mesti memaparkan dashboard dengan ringkasan inventori dan permohonan |
| FR-011 | Pengurusan Daerah | Sistem mesti membenarkan CRUD senarai daerah |
| FR-012 | Pengurusan Kategori | Sistem mesti membenarkan CRUD kategori barang |
| FR-013 | Pengurusan Inventori | Sistem mesti membenarkan CRUD inventori barang dengan atribut: |
| FR-013a | - Nama Barang | Nama barang |
| FR-013b | - Jumlah/Kuantiti | Kuantiti barang |
| FR-013c | - Kondisi Barang | Kondisi barang (baik/rosak/dll) |
| FR-013d | - Status Barang | Status barang (tersedia/dipinjam/dll) |
| FR-013e | - Kategori Barang | Kategori barang |
| FR-013f | - Lokasi Penyimpanan | Lokasi penyimpanan barang |
| FR-013g | - Tarikh Luput | Tarikh luput barang (jika berkaitan) |
| FR-014 | Pengurusan Lokasi | Sistem mesti membenarkan CRUD lokasi penyimpanan |
| FR-015 | Pengurusan Pengguna | Sistem mesti membenarkan CRUD pengguna sistem |
| FR-016 | Pengurusan Permohonan | Sistem mesti membenarkan pengurusan permohonan dan pinjaman |

### 4.4 Modul Tambahan (Cadangan)

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| FR-017 | Notifikasi | Sistem mesti menghantar notifikasi untuk permohonan baru, kelulusan, dan peringatan |
| FR-018 | QR Code | Sistem mesti menjana QR code untuk setiap barang inventori |
| FR-019 | Audit Trail | Sistem mesti merekod setiap transaksi dan perubahan data |
| FR-020 | Laporan | Sistem mesti menyediakan laporan bulanan dan eksport PDF/Excel |
| FR-021 | Approval Workflow | Sistem mesti menyokong aliran kelulusan pelbagai peringkat |
| FR-022 | Dashboard Visualisasi | Sistem mesti memaparkan carta dan graf interaktif |
| FR-023 | Carian Lanjutan | Sistem mesti menyediakan carian dan penapis lanjutan |

---

## 5. Non-Functional Requirements

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| NFR-001 | Prestasi | Masa muat halaman tidak melebihi 3 saat |
| NFR-002 | Keselamatan | Kata laluan mesti di-hash, akses berdasarkan peranan (RBAC) |
| NFR-003 | Kebolehcapaian | Sistem mesti responsif dan boleh diakses melalui pelbagai peranti |
| NFR-004 | Kebolehskalaan | Sistem mesti boleh menampung sehingga 500 pengguna serentak |
| NFR-005 | Ketersediaan | Sistem mesti tersedia 99.5% sepanjang tahun (kecuali penyelenggaraan) |
| NFR-006 | Kebolehselenggaraan | Kod mesti mengikut piawaian dan didokumentasi dengan baik |
| NFR-007 | Backup | Data mesti di-backup secara berkala |

---

## 6. Use Case Scenarios

### Use Case 1: Permohonan Pinjaman Baru
1. User login ke sistem
2. User navigasi ke "Borang Permohonan"
3. User pilih barang yang ingin dipinjam (multi-select)
4. User masukkan kuantiti setiap barang
5. User pilih tarikh pinjam dan tarikh pulang
6. User masukkan tujuan pinjaman
7. User hantar permohonan
8. Sistem rekod permohonan dengan status "Menunggu Kelulusan"
9. Sistem hantar notifikasi kepada Admin

### Use Case 2: Kelulusan Permohonan
1. Admin login ke sistem
2. Admin lihat senarai permohonan baru di dashboard
3. Admin klik pada permohonan untuk lihat detail
4. Admin semak ketersediaan barang
5. Admin lulus atau tolak permohonan
6. Jika lulus, sistem kurangkan stok dan rekod pinjaman
7. Sistem hantar notifikasi kepada pemohon

### Use Case 3: Pengurusan Inventori
1. Admin login ke sistem
2. Admin navigasi ke "Pengurusan Inventori"
3. Admin tambah/ubah/padam barang
4. Sistem kemaskini stok dan rekod perubahan

---

## 7. Keperluan Antaramuka

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| UI-001 | Responsif | Antaramuka mesti responsif untuk desktop, tablet, dan telefon bimbit |
| UI-002 | Bahasa | Antaramuka dalam Bahasa Melayu |
| UI-003 | Tema | Rekabentuk profesional dengan warna korporat JPP |
| UI-004 | Navigasi | Navigasi mudah dan intuitif |
| UI-005 | Mesej Ralat | Mesej ralat yang jelas dan membantu |

---

## 8. Keperluan Integrasi

| ID | Keperluan | Penerangan |
|----|-----------|------------|
| INT-001 | Email | Integrasi dengan sistem email untuk notifikasi |
| INT-002 | Eksport Data | Keupayaan eksport ke PDF dan Excel |

---

## 9. Andajan dan Kebergantungan

- Pengguna mempunyai akses internet dan pelayar web moden
- Data pengguna sedia ada akan dimigrasi ke sistem baru
- Latihan pengguna akan disediakan selepas pelancaran
- Sokongan teknikal disediakan semasa waktu pejabat
