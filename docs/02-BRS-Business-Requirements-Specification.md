# BRS (Business Requirements Specification)
## Sistem Pengurusan Barangan Makmal
### Jabatan Perkhidmatan Pembetungan Sabah (JPP)

---

## Dokumen Versi

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | - | Draf pertama |

---

## 1. Latar Belakang Organisasi

### 1.1 Mengenai JPP Sabah
Jabatan Perkhidmatan Pembetungan Sabah (JPP) adalah sebuah jabatan kerajaan negeri yang bertanggungjawab dalam perkhidmatan pembetungan di seluruh negeri Sabah. Jabatan ini mempunyai Ibu Pejabat (HQ) di Kota Kinabalu dan beberapa pejabat daerah di seluruh Sabah.

### 1.2 Fungsi Makmal JPP
Makmal JPP memainkan peranan penting dalam:
- Analisis kualiti air dan sisa
- Ujian spesifikasi teknikal
- Penyelidikan dan pembangunan
- Pemantauan alam sekitar

Barangan dan peralatan makmal adalah aset penting yang perlu diurus dengan sistematik.

---

## 2. Problem Statement

### 2.1 Isu Semasa
Berdasarkan analisis awal, beberapa isu dikenalpasti dalam pengurusan barangan makmal sedia ada:

| Isu | Kesan |
|-----|-------|
| Tiada sistem berpusat untuk rekod inventori | Data bertaburan, sukar dijejak |
| Proses pinjaman manual (borang fizikal/email) | Lambat, risiko kehilangan rekod |
| Tiada pengesanan status pinjaman real-time | Pegawai daerah tidak tahu status permohonan |
| Stok tidak dikemaskini secara langsung | Risiko pinjaman berlebihan (over-booking) |
| Tiada rekod sejarah pinjaman | Sukar analisis penggunaan barang |
| Proses kelulusan tidak standard | Kelewatan dan kekeliruan |

### 2.2 Kesan Perniagaan
- **Operasi tergendala** - Kelewatan mendapatkan peralatan menjejaskan operasi makmal daerah
- **Pembaziran sumber** - Pembelian barang yang sama akibat ketiadaan rekod pusat
- **Ketidakpatuhan** - Risiko audit akibat rekod tidak lengkap
- **Produktiviti rendah** - Masa terbuang dengan proses manual

---

## 3. Business Objectives

| Objektif | Penerangan | KPI |
|----------|------------|-----|
| OBJ-01 | Memusatkan pengurusan inventori makmal | 100% inventori direkod dalam sistem |
| OBJ-02 | Mempercepatkan proses permohonan pinjaman | Masa pemprosesan < 1 hari bekerja |
| OBJ-03 | Meningkatkan ketelusan status pinjaman | Status real-time, 24/7 boleh diakses |
| OBJ-04 | Mengurangkan kesilapan data | 0% data conflict (over-booking) |
| OBJ-05 | Menyediakan laporan pengurusan tepat | Laporan automatik, boleh dikonfigurasi |
| OBJ-06 | Meningkatkan kecekapan operasi | 50% pengurangan masa proses pinjaman |

---

## 4. Success Metrics

| Metrik | Sasaran | Cara Ukur |
|--------|---------|-----------|
| Masa pemprosesan permohonan | < 24 jam | Log sistem |
| Kadar penggunaan sistem | > 90% staf terlibat | Log login |
| Ketepatan inventori | 100% | Audit berkala |
| Kepuasan pengguna | > 80% | Survey |
| Masa henti sistem | < 0.5% | Uptime monitor |

---

## 5. Process Flow

### 5.1 Proses Semasa (As-Is)

```
Pegawai Daerah -> Emel/Telefon HQ -> Semak Stok Manual -> 
Borang Fizikal -> Pengesahan -> Kelulusan -> Ambil Barang
```

**Kelemahan:**
- Tiada rekod berpusat
- Proses lambat (3-5 hari bekerja)
- Risiko kehilangan borang
- Tiada pengesanan stok masa nyata

### 5.2 Proses Cadangan (To-Be)

```
Pegawai Daerah -> Login Sistem -> Pilih Barang (Online) -> 
Hantar Permohonan -> Notifikasi Admin -> Semak & Lulus/Tolak -> 
Sistem Update Stok -> Notifikasi Pemohon -> Ambil/Hantar Barang
```

**Kelebihan:**
- Semua rekod berpusat
- Proses cepat (< 1 hari)
- Stok dikemaskini automatik
- Status real-time
- Audit trail lengkap

---

## 6. ROI & Justifikasi

### 6.1 Kos Pembangunan (Anggaran)

| Item | Anggaran Kos |
|------|-------------|
| Pembangunan Sistem | - |
| Hosting & Domain (tahunan) | - |
| Latihan Pengguna | - |
| Penyelenggaraan (tahunan) | - |

### 6.2 Penjimatan & Manfaat

| Manfaat | Nilai (Anggaran) |
|---------|------------------|
| Penjimatan masa proses pinjaman | ~80% pengurangan masa |
| Pengurangan pembelian berulang | Penjimatan kos inventori |
| Peningkatan produktiviti staf | Lebih masa untuk tugas utama |
| Ketepatan data & keputusan | Kurang risiko kesilapan |

---

## 7. Risk Assessment

| Risiko | Kebarangkalian | Impak | Mitigasi |
|--------|---------------|-------|----------|
| Perubahan skop | Sederhana | Tinggi | Dokumentasi jelas, change management |
| Kekangan bajet | Rendah | Tinggi | Fasa pembangunan bertahap |
| Penolakan pengguna | Sederhana | Sederhana | Latihan menyeluruh, UI mesra pengguna |
| Isu teknikal | Rendah | Sederhana | Ujian menyeluruh, backup plan |
| Keselamatan data | Rendah | Tinggi | Enkripsi, RBAC, audit trail |

---

## 8. Scope & Boundaries

### 8.1 In-Scope
- Pengurusan inventori barangan makmal HQ
- Permohonan pinjaman dari Daerah ke HQ
- Pengurusan kelulusan pinjaman
- Pengurusan pengguna, daerah, kategori, lokasi
- Laporan dan analitik asas
- Notifikasi email

### 8.2 Out-of-Scope (Fasa Akan Datang)
- Pengurusan aset tetap (fixed assets)
- Sistem pembelian/pembekal
- Integrasi dengan sistem kewangan
- Aplikasi mobile native
- Pengurusan penyelenggaraan alat

---

## 9. Stakeholder Requirements

| Stakeholder | Keperluan Utama | Prioriti |
|-------------|-----------------|----------|
| Pegawai Daerah | Borang mudah, status jelas | Tinggi |
| Admin HQ | Dashboard komprehensif | Tinggi |
| Pengurusan | Laporan tepat | Sederhana |
| IT | Sistem stabil, mudah selenggara | Tinggi |

---

## 10. Assumptions & Constraints

### 10.1 Assumptions
- Semua daerah mempunyai akses internet
- Pengguna mempunyai kemahiran IT asas
- Data sedia ada boleh dimigrasi
- Sokongan pengurusan atasan

### 10.2 Constraints
- Tempoh pembangunan 3-4 bulan
- Bajet terhad
- Sumber manusia terhad
- Perlu mematuhi peraturan kerajaan

---

## 11. Regulatory Compliance

Sistem perlu mematuhi:
- **Pekeliling Perkhidmatan** - Pengurusan aset kerajaan
- **Akta Arkib Negara** - Penyimpanan rekod
- **PIAI (Pekeliling ICT)** - Piawaian ICT kerajaan
- **Akta Perlindungan Data Peribadi** - Jika berkaitan
