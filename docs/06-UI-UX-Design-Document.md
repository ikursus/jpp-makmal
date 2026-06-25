# UI/UX Design Document
## Sistem Pengurusan Barangan Makmal
### Jabatan Perkhidmatan Pembetungan Sabah (JPP)

---

## Dokumen Versi

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | - | Draf pertama |

---

## 1. Design Principles

| Prinsip | Penerangan |
|---------|------------|
| **Konsisten** | Rekabentuk seragam merentas semua halaman |
| **Ringkas** | Antaramuka minimal, fokus pada fungsi utama |
| **Responsif** | Berfungsi pada desktop, tablet, dan mudah alih |
| **Mesra Pengguna** | Mudah difahami, navigasi intuitif |
| **Korporat** | Profesional, mencerminkan imej JPP |
| **Accessibility** | Kontras warna sesuai, font readable |

---

## 2. Design System

### 2.1 Color Palette

```
Primary Colors:
┌──────────────────┐
│ #003366 (Navy)   │ ← Warna utama JPP
│ Diguna untuk:    │
│ - Header/Footer  │
│ - Sidebar aktif  │
│ - Button utama   │
└──────────────────┘

┌──────────────────┐
│ #006633 (Green)  │ ← Warna aksen JPP
│ Diguna untuk:    │
│ - Status "Aktif" │
│ - Button success │
│ - Badge lulus    │
└──────────────────┘

Neutral Colors:
┌──────────────────┐
│ #1F2937 (Dark)   │ ← Text utama
│ #6B7280 (Gray)   │ ← Text sekunder
│ #E5E7EB (Light)  │ ← Border/divider
│ #F9FAFB (White)  │ ← Background
└──────────────────┘

Status Colors:
┌──────────────────┐
│ #10B981 (Green)  │ ← Success / Diluluskan  │
│ #F59E0B (Yellow) │ ← Warning / Menunggu    │
│ #EF4444 (Red)    │ ← Error / Ditolak       │
│ #3B82F6 (Blue)   │ ← Info / Dipinjam      │
└──────────────────┘
```

### 2.2 Typography

| Elemen | Font | Saiz | Weight |
|--------|------|------|--------|
| H1 - Page Title | Inter / sans-serif | 2xl (24px) | Bold |
| H2 - Section Title | Inter / sans-serif | xl (20px) | Semibold |
| H3 - Card Title | Inter / sans-serif | lg (18px) | Semibold |
| Body Text | Inter / sans-serif | base (16px) | Normal |
| Small / Label | Inter / sans-serif | sm (14px) | Medium |
| Badge / Tag | Inter / sans-serif | xs (12px) | Medium |

### 2.3 Spacing System

| Scale | px | Diguna Untuk |
|-------|-----|-------------|
| 1 | 4px | Padding kecil, gap icon |
| 2 | 8px | Padding button, gap small |
| 3 | 12px | Padding card, gap form |
| 4 | 16px | Padding section, margin |
| 5 | 20px | Margin section |
| 6 | 24px | Padding page, gap large |
| 8 | 32px | Margin page section |
| 10 | 40px | Section separator |

### 2.4 Border Radius

| Scale | Value | Diguna Untuk |
|-------|-------|-------------|
| sm | 4px | Input fields, small cards |
| md | 8px | Cards, modals |
| lg | 12px | Main containers |
| xl | 16px | Modals large |
| full | 9999px | Avatars, badges |

---

## 3. Page Layouts & Wireframes

### 3.1 Public Page - Halaman Utama (Landing Page)

```
┌──────────────────────────────────────────────────────────────┐
│  [Logo JPP]              Sistem Pengurusan Barangan Makmal   │
│                          [Login Button]                      │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │                                                        │ │
│  │   🏢 Selamat Datang ke Sistem Pengurusan               │ │
│  │      Barangan Makmal JPP Sabah                         │ │
│  │                                                        │ │
│  │   Sistem ini memudahkan pengurusan inventori dan       │ │
│   │   permohonan pinjaman barangan makmal antara          │ │
│   │   Ibu Pejabat dan Pejabat Daerah.                     │ │
│  │                                                        │ │
│  │                               [Log Masuk]              │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐       │
│  │ 📦      │  │ 📋      │  │ ✅      │  │ 📊      │       │
│  │ Urus    │  │ Pinjam  │  │Status   │  │ Laporan │       │
│  │Inventori│  │ Barang  │  │ Real-time│  │ & Analitik│    │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘       │
│                                                              │
├──────────────────────────────────────────────────────────────┤
│  © 2026 JPP Sabah. Hak Cipta Terpelihara.                   │
└──────────────────────────────────────────────────────────────┘
```

### 3.2 Halaman Login

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│                       ┌─────────────────────┐                │
│                       │                     │                │
│                       │   [Logo JPP]        │                │
│                       │                     │                │
│                       │   Log Masuk         │                │
│                       │                     │                │
│                       │   Emel              │                │
│                       │   ┌─────────────┐   │                │
│                       │   │             │   │                │
│                       │   └─────────────┘   │                │
│                       │                     │                │
│                       │   Kata Laluan       │                │
│                       │   ┌─────────────┐   │                │
│                       │   │  ••••••••   │   │                │
│                       │   └─────────────┘   │                │
│                       │                     │                │
│                       │   [✓] Ingat Saya    │                │
│                       │                     │                │
│                       │   ┌─────────────┐   │                │
│                       │   │  LOG MASUK   │   │                │
│                       │   └─────────────┘   │                │
│                       │                     │                │
│                       │   Lupa kata laluan? │                │
│                       │                     │                │
│                       └─────────────────────┘                │
│                                                              │
└──────────────────────────────────────────────────────────────┘

**Validation States:**
┌─────────────────────┐
│  ❌ Emel diperlukan  │ ← Error state
└─────────────────────┘

┌─────────────────────┐
│  ✅ Log masuk berjaya│ ← Success (redirect)
└─────────────────────┘
```

### 3.3 User Dashboard

```
┌──────────────────────────────────────────────────────────────┐
│  [☰]  Sistem Pengurusan Barangan Makmal    👤 Ahmad Bin I. │
├──────┬───────────────────────────────────────────────────────┤
│      │                                                       │
│ Menu │   Dashboard User                                     │
│ Side │                                                       │
│      │   ┌──────────┐  ┌──────────┐  ┌──────────┐          │
│ 🏠   │   │ 📦       │  │ 📋       │  │ ⏳       │          │
│ Dash │   │ Pinjaman │  │Permohonan│  │ Menunggu │          │
│ board│   │ Aktif: 2 │  │Dilulus: 3│  │ : 1      │          │
│      │   └──────────┘  └──────────┘  └──────────┘          │
│ 📦   │                                                       │
│ Sen. │   Pinjaman Aktif Anda                                 │
│ Inven│   ┌──────────────────────────────────────────────┐    │
│ tori │   │ #LN0001 - Mikroskop (2 unit)                 │    │
│      │   │ 📅 15-20 Jun 2026   🟢 Dipinjam             │    │
│      │   ├──────────────────────────────────────────────┤    │
│ 📝   │   │ #LN0002 - Spektrofotometer (1 unit)          │    │
│ Boro │   │ 📅 18-25 Jun 2026   🟢 Dipinjam             │    │
│ ng   │   └──────────────────────────────────────────────┘    │
│      │                                                       │
│ 📋   │   Permohonan Terkini                                  │
│ Stat │   ┌──────────────────────────────────────────────┐    │
│ us   │   │ #LA-20260620-001 - 3 barang                  │    │
│      │   │ 📅 22-28 Jun 2026   🟡 Menunggu             │    │
│      │   └──────────────────────────────────────────────┘    │
│ 👤   │                                                       │
│ Profi│                                                       │
│ l    │                                                       │
│      │                                                       │
│ 🚪   │                                                       │
│ Log o│                                                       │
│ ut   │                                                       │
└──────┴───────────────────────────────────────────────────────┘
```

### 3.4 Borang Permohonan Pinjaman

```
┌──────────────────────────────────────────────────────────────┐
│  [☰]  Sistem Pengurusan Barangan Makmal    👤 Ahmad Bin I. │
├──────┬───────────────────────────────────────────────────────┤
│      │                                                       │
│ Menu │   📝 Permohonan Pinjaman Baru                        │
│ Side │                                                       │
│      │   ┌──────────────────────────────────────────────┐    │
│      │   │ Maklumat Pemohon                             │    │
│      │   │ Nama     : Ahmad bin Ismail                  │    │
│      │   │ Daerah   : Sandakan                          │    │
│      │   │ Emel     : ahmad@jpp.gov.my                  │    │
│      │   └──────────────────────────────────────────────┘    │
│      │                                                       │
│      │   Pilih Barang                                        │
│      │   ┌──────────────────────────────────────────────┐    │
│      │   │ 🔍 Cari barang...                        [Q] │    │
│      │   ├──────────────────────────────────────────────┤    │
│      │   │ [✓] Mikroskop Elektron          Qty: [ 2 ]  │    │
│      │   │ [✓] Spektrofotometer            Qty: [ 1 ]  │    │
│      │   │ [ ] Centrifuge                  Qty: [ 0 ]  │    │
│      │   │ [ ] Autoklaf                    Qty: [ 0 ]  │    │
│      │   │ [✓] pH Meter                    Qty: [ 3 ]  │    │
│      │   │ [ ] Oven Makmal                 Qty: [ 0 ]  │    │
│      │   │ [✓] Timbangan Analitik          Qty: [ 1 ]  │    │
│      │   └──────────────────────────────────────────────┘    │
│      │                                                       │
│      │   Tarikh Pinjaman                                     │
│      │   Daripada: [ 22/06/2026 ]  Hingga: [ 28/06/2026 ]   │
│      │                                                       │
│      │   Tujuan Pinjaman                                     │
│      │   ┌──────────────────────────────────────────────┐    │
│      │   │ Ujian kualiti air sampel Sungai Kinabatangan │    │
│      │   └──────────────────────────────────────────────┘    │
│      │                                                       │
│      │                         [Batal]  [Hantar Permohonan]  │
│      │                                                       │
└──────┴───────────────────────────────────────────────────────┘
```

### 3.5 Admin Dashboard

```
┌──────────────────────────────────────────────────────────────┐
│  [☰]  Sistem Pengurusan Barangan Makmal    👤 Siti Binti A.│
├──────┬───────────────────────────────────────────────────────┤
│      │                                                       │
│ Menu │   Dashboard Pentadbir                                 │
│ Side │                                                       │
│      │   ┌──────────┐  ┌──────────┐  ┌──────────┐          │
│ 🏠   │   │ 📦       │  │ 📋       │  │ ⏳       │          │
│ Dash │   │ Jumlah   │  │Permohonan│  │ Menunggu │          │
│ board│   │Barang:500│  │ : 120    │  │ : 5      │          │
│      │   └──────────┘  └──────────┘  └──────────┘          │
│      │                                                       │
│ 🌍   │   Ringkasan Inventori                                 │
│ Daer │   ┌──────────────────────────────────────────────┐    │
│ ah   │   │                                            │    │
│      │   │   [Bar Chart: Tersedia vs Dipinjam]         │    │
│ 📂   │   │                                            │    │
│ Kate │   └──────────────────────────────────────────────┘    │
│ gori │                                                       │
│      │   Permohonan Menunggu Kelulusan                       │
│ 📦   │   ┌──────────────────────────────────────────────┐    │
│ Inve │   │ #LA001 - Sandakan - 3 barang - 22/06      │    │
│ ntori│   │                                [✅][❌]    │    │
│      │   ├──────────────────────────────────────────────┤    │
│ 📍   │   │ #LA002 - Tawau - 2 barang - 22/06          │    │
│ Loka │   │                                [✅][❌]    │    │
│ si   │   ├──────────────────────────────────────────────┤    │
│      │   │ #LA003 - Keningau - 5 barang - 21/06       │    │
│ 👥   │   │                                [✅][❌]    │    │
│ Peng │   └──────────────────────────────────────────────┘    │
│ guna │                                                       │
│      │   Barangan Hampir Luput                               │
│ 📋   │   ┌──────────────────────────────────────────────┐    │
│ Perm │   │ ⚠️ Reagent X - Luput: 25 Jun 2026           │    │
│ ohon │   │ ⚠i Buffer Solution - Luput: 30 Jun 2026      │    │
│ an   │   └──────────────────────────────────────────────┘    │
│      │                                                       │
│ 📊   │                                                       │
│ Lapor│                                                       │
│ an   │                                                       │
│ 🚪   │                                                       │
│ Log o│                                                       │
│ ut   │                                                       │
└──────┴───────────────────────────────────────────────────────┘
```

### 3.6 Pengurusan Inventori (Admin)

```
┌──────────────────────────────────────────────────────────────┐
│  [☰]  Sistem Pengurusan Barangan Makmal    👤 Siti Binti A.│
├──────┬───────────────────────────────────────────────────────┤
│      │                                                       │
│ Menu │   📦 Pengurusan Inventori                 [+ Tambah] │
│ Side │                                                       │
│      │   🔍 Cari barang...       [Kategori: Semua] [Status] │
│      │                                                       │
│      │   ┌────┬────────────┬──────┬────────┬────────┬─────┐ │
│      │   │ #  │ Nama Barang│ Qty  │Status  │Kategori│Act  │ │
│      │   ├────┼────────────┼──────┼────────┼────────┼─────┤ │
│      │   │ 1  │ Mikroskop  │ 5    │🟢Tersed│ Optik  │📝🗑️ │ │
│      │   │ 2  │ Spektro.   │ 3    │🟡Dipinj│ Analit │📝🗑️ │ │
│      │   │ 3  │ Centrifuge │ 2    │🟢Tersed│ Pusat  │📝🗑️ │ │
│      │   │ 4  │ Autoklaf   │ 1    │🔴Rosak │ Steril │📝🗑️ │ │
│      │   │ 5  │ pH Meter   │ 10   │🟢Tersed│ Ukur   │📝🗑️ │ │
│      │   │ 6  │ Oven       │ 2    │🟢Tersed│ Pemanas│📝🗑️ │ │
│      │   │ 7  │ Timbangan  │ 4    │🟡Dipinj│ Timbang│📝🗑️ │ │
│      │   └────┴────────────┴──────┴────────┴────────┴─────┘ │
│      │                                                       │
│      │   Papar 1-7 dari 500 rekod          < 1 2 3 ... 72 > │
│      │                                                       │
└──────┴───────────────────────────────────────────────────────┘
```

### 3.7 Detail Permohonan (Admin)

```
┌──────────────────────────────────────────────────────────────┐
│  [☰]  Sistem Pengurusan Barangan Makmal                     │
├──────┬───────────────────────────────────────────────────────┤
│      │                                                       │
│      │   📋 Permohonan #LA-20260620-001                      │
│      │                                                       │
│      │   Status: 🟡 Menunggu Kelulusan                       │
│      │                                                       │
│      │   ┌──────────────────────┐ ┌──────────────────────┐   │
│      │   │ Maklumat Pemohon     │ │ Maklumat Pinjaman    │   │
│      │   │──────────────────────│ │──────────────────────│   │
│      │   │ Nama: Ahmad Ismail   │ │ Tarikh: 22-28 Jun   │   │
│      │   │ Daerah: Sandakan     │ │ Durasi: 7 hari      │   │
│      │   │ Emel: ahmad@jpp.my   │ │ Tujuan: Ujian air   │   │
│      │   └──────────────────────┘ └──────────────────────┘   │
│      │                                                       │
│      │   Barang Dipohon                                       │
│      │   ┌────┬──────────────────────┬──────┬─────────────┐  │
│      │   │ #  │ Barang               │ Qty  │ Stok Ada   │  │
│      │   ├────┼──────────────────────┼──────┼─────────────┤  │
│      │   │ 1  │ Mikroskop Elektron   │ 2    │ ✅ 5 unit   │  │
│      │   │ 2  │ Spektrofotometer     │ 1    │ ✅ 3 unit   │  │
│      │   │ 3  │ pH Meter             │ 3    │ ✅ 10 unit  │  │
│      │   │ 4  │ Timbangan Analitik   │ 1    │ ✅ 4 unit   │  │
│      │   └────┴──────────────────────┴──────┴─────────────┘  │
│      │                                                       │
│      │   Catatan Pelulus:                                    │
│      │   ┌──────────────────────────────────────────────┐    │
│      │   │                                              │    │
│      │   └──────────────────────────────────────────────┘    │
│      │                                                       │
│      │               [← Kembali]  [❌ Tolak]  [✅ Lulus]    │
│      │                                                       │
└──────┴───────────────────────────────────────────────────────┘
```

---

## 4. User Flow Diagrams

### 4.1 User Flow: Permohonan Pinjaman

```
START
  │
  ▼
Halaman Utama (Public)
  │
  ▼
Login (Masukkan Emel & Password)
  │
  ├──❌ Gagal → Mesej Ralat → Cuba Lagi
  │
  ▼✅ Berjaya
  │
User Dashboard
  │
  ▼
Klik "Borang Permohonan"
  │
  ▼
Borang Permohonan
  ├─ Pilih Barang (Multi-select + Quantity)
  ├─ Pilih Tarikh (Dari - Hingga)
  ├─ Isi Tujuan Pinjaman
  │
  ▼
Klik "Hantar Permohonan"
  │
  ├──❌ Validation Error → Betulkan medan
  │
  ▼✅ Berjaya
  │
Halaman Detail Permohonan
  └─ Status: 🟡 Menunggu Kelulusan
  └─ Notifikasi emel dihantar ke Admin
  
WAIT untuk kelulusan Admin
```

### 4.2 User Flow: Kelulusan Admin

```
Admin Login
  │
  ▼
Admin Dashboard
  └─ Lihat "Permohonan Menunggu"
  │
  ▼
Klik pada permohonan
  │
  ▼
Detail Permohonan
  ├─ Semak maklumat pemohon
  ├─ Semak barang & ketersediaan stok
  ├─ Masukkan catatan (optional)
  │
  ▼
Pilih Tindakan:
  │
  ├── ✅ Lulus
  │     ├─ Sistem rekod pinjaman
  │     ├─ Sistem kurangkan stok
  │     ├─ Status → Dipinjam
  │     └─ Notifikasi emel ke pemohon
  │
  └── ❌ Tolak
        ├─ Masukkan sebab penolakan
        ├─ Status → Ditolak
        └─ Notifikasi emel ke pemohon
```

### 4.3 User Flow: Pengembalian Barang

```
Admin navigasi ke "Pinjaman Aktif"
  │
  ▼
Senarai pinjaman aktif
  │
  ▼
Klik "Pulang" pada pinjaman
  │
  ▼
Borang Pengembalian
  ├─ Pilih barang yang dipulang
  ├─ Semak kondisi barang selepas
  ├─ Masukkan kuantiti dipulang
  │
  ▼
Klik "Sahkan Pulang"
  │
  ▼
Sistem:
  ├─ Update status pinjaman → Dipulangkan
  ├─ Tambah stok (restore quantity)
  ├─ Rekod kondisi selepas (jika berubah)
  ├─ Rekod dalam item_conditions
  └─ Notifikasi ke pemohon
```

---

## 5. Component Library

### 5.1 Button Components

```
┌─────────────────────────────────────────────────────────────┐
│  Button Variants                                             │
│                                                              │
│  [Primari]    [Secondary]    [Success/✅]  [Danger/❌]      │
│                                                              │
│  [Small]      [Normal]       [Large]                         │
│                                                              │
│  [Loading...]  [Disabled]                                    │
└─────────────────────────────────────────────────────────────┘
```

### 5.2 Badge / Status Components

```
Status Badges:
┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────────┐
│ 🟢Tersedia│  │ 🟡Dipinjam│  │ 🔴Rosak  │  │ ⚪️Disimpan  │
└──────────┘  └──────────┘  └──────────┘  └──────────────┘

┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ 🟡 Menunggu   │  │ ✅ Diluluskan │  │ ❌ Ditolak    │
└──────────────┘  └──────────────┘  └──────────────┘
```

### 5.3 Card Components

```
┌──────────────────────────────────────┐
│  ┌────────────────────────────────┐  │
│  │  Statistic Card                │  │
│  │  [Icon]  Title                 │  │
│  │        42                      │  │
│  │         ↑ 12% dari bulan lepas │  │
│  └────────────────────────────────┘  │
│                                      │
│  ┌────────────────────────────────┐  │
│  │  Information Card              │  │
│  │  ┌─────┬──────────────────┐   │  │
│  │  │Label│ Value            │   │  │
│  │  ├─────┼──────────────────┤   │  │
│  │  │Label│ Value            │   │  │
│  │  └─────┴──────────────────┘   │  │
│  │  [Action Button]              │  │
│  └────────────────────────────────┘  │
└──────────────────────────────────────┘
```

### 5.4 Modal / Dialog Components

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│  ┌────────────────────────────────────────────────────┐      │
│  │  ⚠️ Pengesahan                                    │      │
│  │                                                  │      │
│  │  Adakah anda pasti mahu meluluskan permohonan    │      │
│  │  ini?                                            │      │
│  │                                                  │      │
│  │                     [Tidak]  [Ya, Luluskan]      │      │
│  └────────────────────────────────────────────────────┘      │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 5.5 Form Input Components

```
Normal State:
┌──────────────────────┐
│ Label                │
│ ┌──────────────────┐ │
│ │ Input text here  │ │
│ └──────────────────┘ │
└──────────────────────┘

Focus State:
┌──────────────────────┐
│ Label                │
│ ┌──────────────────┐ │
│ │ Input text here▎ │ │ ← Blue border
│ └──────────────────┘ │
└──────────────────────┘

Error State:
┌──────────────────────┐
│ Label                │
│ ┌──────────────────┐ │
│ │ ❌ Wrong value   │ │ ← Red border
│ └──────────────────┘ │
│ Mesej ralat di sini  │ ← Red text
└──────────────────────┘

Success State:
┌──────────────────────┐
│ Label                │
│ ┌──────────────────┐ │
│ │ ✅ Correct value │ │ ← Green border
│ └──────────────────┘ │
└──────────────────────┘

Disabled State:
┌──────────────────────┐
│ Label                │
│ ┌──────────────────┐ │
│ │ Disabled input   │ │ ← Gray bg
│ └──────────────────┘ │
└──────────────────────┘
```

### 5.6 Table Components

```
┌─────────────────────────────────────────────────────────────┐
│  🔍 Search...        [Filter ▼]       [Export ▼]  [+Add]   │
│                                                              │
│  ┌────┬────────────┬──────┬────────┬──────────────────────┐ │
│  │ #  │ Name       │ Qty  │ Status │ Actions              │ │
│  ├────┼────────────┼──────┼────────┼──────────────────────┤ │
│  │ 1  │ Item A     │ 10   │ 🟢 Ok  │ [Edit] [Delete]      │ │
│  │ 2  │ Item B     │ 5    │ 🟡 Low │ [Edit] [Delete]      │ │
│  └────┴────────────┴──────┴────────┴──────────────────────┘ │
│                                                              │
│  Showing 1-2 of 2 records            < 1 >                  │
└─────────────────────────────────────────────────────────────┘
```

### 5.7 Sidebar Navigation

```
┌──────────────────┐
│ [☰] Menu Utama   │
│                  │
│ 🏠 Dashboard     │ ← Active state (different bg)
│                  │
│ 🌍 Daerah        │
│                  │
│ 📂 Kategori      │
│                  │
│ 📦 Inventori     │
│                  │
│ 📍 Lokasi        │
│                  │
│ 👥 Pengguna      │
│                  │
│ 📋 Permohonan    │
│                  │
│ 📊 Laporan       │
│                  │
│ ────────────     │
│                  │
│ 👤 Profil        │
│                  │
│ 🚪 Log Keluar    │
└──────────────────┘
```

---

## 6. Responsive Design Breakpoints

| Breakpoint | Lebar Skrin | Sasaran Peranti |
|------------|-------------|-----------------|
| xs | < 640px | Telefon bimbit |
| sm | 640px - 767px | Telefon besar |
| md | 768px - 1023px | Tablet |
| lg | 1024px - 1279px | Desktop kecil |
| xl | 1280px+ | Desktop besar |

### 6.1 Responsive Behaviour

| Komponen | Desktop (lg+) | Tablet (md) | Mobile (sm-) |
|----------|--------------|-------------|--------------|
| Sidebar | Visible | Collapsible | Hidden (hamburger) |
| Table | Full columns | Kurang columns | Card view |
| Cards | 4 columns | 2 columns | 1 column |
| Form | 2 columns | 1 column | 1 column |
| Header | Full | Compact | Minimal |

---

## 7. Loading States & Animations

### 7.1 Loading States

```
Loading Skeleton:
┌───────────────────────────────────────┐
│  ┌─────────────────────────────────┐  │
│  │  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓        │  │ ← Skeleton loading
│  │  ▓▓▓▓▓▓▓▓▓▓▓▓▓                  │  │
│  │  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓      │  │
│  └─────────────────────────────────┘  │
└───────────────────────────────────────┘

Spinner:
  [ ⟳ ] Loading...
```

### 7.2 Transition Animations

| Elemen | Animasi | Duration |
|--------|---------|----------|
| Page transition | Fade in | 200ms |
| Modal open | Scale up + fade | 300ms |
| Sidebar toggle | Slide right | 250ms |
| Button hover | Background change | 150ms |
| Table row hover | Background highlight | 100ms |
| Notification | Slide from top | 300ms |
| Toast | Slide from right | 400ms |

---

## 8. Notification & Feedback

### 8.1 Toast Notifications

```
┌──────────────────────────────────────────────┐
│  Top-right position                          │
│                                              │
│  ┌──────────────────┐  ┌──────────────────┐  │
│  │ ✅ Permohonan    │  │ ❌ Ralat        │  │
│  │    berjaya dihantar│  │ Sila cuba lagi  │  │
│  └──────────────────┘  └──────────────────┘  │
│                                              │
│  ┌──────────────────┐  ┌──────────────────┐  │
│  │ ℹ️ Info          │  │ ⚠️ Amaran       │  │
│  └──────────────────┘  └──────────────────┘  │
└──────────────────────────────────────────────┘
```

### 8.2 Confirmation Dialogs

```
┌──────────────────────────────────────────────┐
│  ⚠️ Pengesahan                               │
│                                              │
│  Anda pasti mahu menolak permohonan ini?     │
│                                              │
│  Sebab: [___________________________]        │
│                                              │
│                [Kembali]     [Ya, Tolak]     │
└──────────────────────────────────────────────┘
```

---

## 9. Accessibility Guidelines

| Guideline | Implementasi |
|-----------|-------------|
| **WCAG 2.1 AA** | Colour contrast ratio ≥ 4.5:1 |
| **Keyboard Navigation** | All functions accessible via keyboard |
| **Focus Indicators** | Visible focus ring on all interactive elements |
| **ARIA Labels** | All icons & buttons have aria-labels |
| **Screen Reader** | Semantic HTML, proper heading hierarchy |
| **Font Size** | Minimum 16px for body text |
| **Touch Targets** | Minimum 44x44px for mobile buttons |

---

## 10. Empty States & Error Pages

### 10.1 Empty State

```
┌──────────────────────────────────────────────┐
│                                              │
│              📭 Tiada Rekod                   │
│                                              │
│     Belum ada permohonan pinjaman lagi.       │
│     Klik butang di bawah untuk memohon.       │
│                                              │
│              [📝 Buat Permohonan]            │
│                                              │
└──────────────────────────────────────────────┘
```

### 10.2 404 Page

```
┌──────────────────────────────────────────────┐
│                                              │
│              404 - Halaman Tidak Dijumpai     │
│                                              │
│     Maaf, halaman yang anda cari tidak wujud.│
│     Mungkin URL telah ditukar atau dipadam.  │
│                                              │
│              [🏠 Kembali ke Dashboard]       │
│                                              │
└──────────────────────────────────────────────┘
```

### 10.3 403 Page

```
┌──────────────────────────────────────────────┐
│                                              │
│              🔒 Akses Dinafikan              │
│                                              │
│     Anda tidak mempunyai kebenaran untuk     │
│     mengakses halaman ini.                   │
│                                              │
│              [🏠 Kembali ke Dashboard]       │
│                                              │
└──────────────────────────────────────────────┘
```

### 10.4 Error 500 Page

```
┌──────────────────────────────────────────────┐
│                                              │
│            ⚠️ Ralat Sistem                   │
│                                              │
│     Maaf, terdapat ralat sistem. Sila cuba   │
│     sebentar lagi atau hubungi pentadbir.    │
│                                              │
│              [🔄 Cuba Semula]                │
│                                              │
└──────────────────────────────────────────────┘
