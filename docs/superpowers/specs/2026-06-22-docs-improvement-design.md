# Spec: Pembaikan Set Dokumentasi — Sistem Pengurusan Barangan Makmal JPP

| Versi | Tarikh | Pengarang | Perubahan |
|-------|--------|-----------|-----------|
| 1.0 | 22 Jun 2026 | Brainstorming session | Spec pertama pembaikan dokumentasi |

---

## 1. Konteks & Keadaan Semasa

Set dokumentasi di `docs/` mengandungi 6 dokumen perancangan pra-pembangunan:

- `01-URS-User-Requirements-Specification.md`
- `02-BRS-Business-Requirements-Specification.md`
- `03-SRS-Software-Requirements-Specification.md`
- `04-ERD-Entity-Relationship-Diagram.md`
- `05-Technical-Architecture-Document.md`
- `06-UI-UX-Design-Document.md`

Kod aplikasi masih **Laravel 13 skeleton kosong** (hanya `app/Models/User.php`, controller & migration default). Justeru dokumen ini adalah rujukan yang akan memandu pembinaan sistem sebenar.

**Tujuan set dokumen (disahkan):** Rujukan pembangunan. Maka **ketepatan dan konsistensi mengatasi segala keutamaan lain** — developer mesti boleh implement terus tanpa keraguan.

**Skop pembaikan (disahkan, keempat-empat):**
1. Betulkan ketakkonsistenan antara dokumen
2. Lengkapkan jurang spesifikasi
3. Tambah dokumen baharu
4. Naik taraf format

## 2. Matlamat & Bukan-Matlamat

### 2.1 Matlamat
- Hapuskan semua percanggahan antara dokumen supaya satu sumber kebenaran wujud bagi setiap fakta.
- Lengkapkan keperluan yang ditunjuk dalam wireframe/perkhidmatan tetapi tiada spesifikasi.
- Selesaikan ambiguiti reka bentuk yang menghalang pembinaan (notifikasi, state machine, concurrency).
- Tambah artifak yang mengikat set & menyokong verifikasi (indeks, traceability, test plan, decision log).
- Tingkatkan kebolehbacaan & kebolehselenggaraan gambarajah.

### 2.2 Bukan-Matlamat
- **Tiada penulisan kod aplikasi** dalam skop ini — hanya dokumentasi.
- Tiada perubahan kepada keputusan teras seni bina (Laravel 13, MySQL, Spatie, Blade + Tailwind kekal).
- Tiada penstrukturan semula besar-besaran fail dokumen sedia ada (struktur kekal kerana sudah baik).

## 3. Keputusan Reka Bentuk (Dikunci)

Direkod sebagai ADR dalam `09-Decision-Log.md`.

| ID | Keputusan | Rasional | Kesan dokumen |
|----|-----------|----------|---------------|
| **D1** | Notifikasi guna **Laravel native** (channel `database` + `mail`) | Padan dengan tech stack "Laravel Notification + Mail"; kurang kod custom; guna `$user->notify()` | ERD: skema `notifications` native; Tech Arch: Notification classes |
| **D2** | State machine **handoff** (application → loan) | Pemisahan kemas & normalized; elak status bertindih dua jadual | ERD enum; SRS state diagram; Tech Arch services |
| **D3** | Penamaan route **plural** (konvensyen Laravel resource) | Konsisten dengan `Route::resource`; hapus konflik singular/plural | SRS URL; Tech Arch web.php |
| **D4** | Over-booking dikawal dengan **`DB::transaction` + `lockForUpdate`** | Capai OBJ-04 "0% over-booking" secara konkrit | SRS §Concurrency; Tech Arch §Services |
| **D5** | Audit kekal **jadual `audit_logs` custom** | Sudah direka kemas; elak ubah skop | ERD tiada perubahan; alternatif `spatie/activitylog` dicatat dalam decision log |

### 3.1 Butiran D1 — Skema `notifications` native Laravel
Ganti skema custom (user_id/title/message/is_read) dengan skema native:

| Column | Type | Constraints |
|--------|------|-------------|
| id | UUID/CHAR(36) | PK |
| type | VARCHAR | NOT NULL (kelas Notification) |
| notifiable_type | VARCHAR | NOT NULL (morph) |
| notifiable_id | BIGINT UNSIGNED | NOT NULL (morph) |
| data | JSON/TEXT | NOT NULL |
| read_at | TIMESTAMP | NULL |
| created_at / updated_at | TIMESTAMP | NULL |

Index: `(notifiable_type, notifiable_id)`. Tajuk/mesej notifikasi disimpan dalam `data` (JSON). Dipapar in-app (panel bell) + dihantar emel.

### 3.2 Butiran D2 — State machine handoff
- `loan_applications.status` ENUM = `{menunggu, diluluskan, ditolak, dibatalkan}` (buang `dipinjam`, `dikembalikan`).
- `loans.status` ENUM = `{aktif, dipulangkan, terlewat}` (kekal).
- Bila permohonan **diluluskan** → rekod `loans` dicipta; status permohonan kekal `diluluskan` (terminal untuk permohonan). Kitaran pinjam seterusnya dijejak pada `loans`.

### 3.3 Butiran D4 — Corak concurrency
```php
DB::transaction(function () use ($itemId, $qty) {
    $item = Item::whereKey($itemId)->lockForUpdate()->first();
    if ($item->available_quantity < $qty) {
        throw new InsufficientStockException();
    }
    $item->decrement('available_quantity', $qty);
    // cipta loan_item ...
});
```
Restore stok semasa pemulangan dibungkus dalam transaction yang sama coraknya.

## 4. Pembetulan Merentas-Dokumen (Cross-Cutting)

### 4.1 Skema FR tunggal (canonical)
Adopsi penomboran URS sebagai kanonik; SRS mesti ikut sama. Selesaikan konflik `FR-003` (URS="Autentikasi" vs SRS="Logout").

| FR | Tajuk | Modul |
|----|-------|-------|
| FR-001 | Halaman Utama | Public |
| FR-002 | Halaman Login (papar borang) | Public |
| FR-003 | Autentikasi (POST, validate, redirect by role) | Auth |
| FR-004 | Dashboard User | User |
| FR-005 | Senarai Inventori (read-only) | User |
| FR-006 | Borang Permohonan Pinjaman | User |
| FR-007 | Detail Permohonan | User |
| FR-008 | Profil User | User |
| FR-009 | Logout | User |
| FR-010 | Dashboard Admin | Admin |
| FR-011 | Pengurusan Daerah | Admin |
| FR-012 | Pengurusan Kategori | Admin |
| FR-013 | Pengurusan Inventori | Admin |
| FR-014 | Pengurusan Lokasi Penyimpanan | Admin |
| FR-015 | Pengurusan Pengguna | Admin |
| FR-016 | Pengurusan Permohonan & Pinjaman | Admin |
| FR-017 | Notifikasi (in-app + emel) | Tambahan |
| FR-018 | QR Code | Tambahan |
| FR-019 | Audit Trail | Tambahan |
| FR-020 | Laporan & Analitik | Tambahan |
| FR-021 | Approval Workflow | Tambahan |
| FR-022 | Dashboard Visualisasi (carta) | Tambahan |
| FR-023 | Carian & Penapis Lanjutan | Tambahan |
| **FR-024** | **Lupa & Reset Kata Laluan** *(baharu)* | Auth |
| **FR-025** | **Alert Luput & Low-Stock** *(baharu)* | Tambahan |

### 4.2 Tambahan keperluan
- **FR-024 Lupa/Reset Kata Laluan** — guna password reset Laravel + jadual `password_reset_tokens` (sedia ada dalam migration default). Route: `password.request`, `password.email`, `password.reset`, `password.update`.
- **FR-025 Alert** — notifikasi luput barang & low-stock kepada Admin (selaras dengan wireframe Admin Dashboard "Barangan Hampir Luput").
- **FR-017** dikemas: notifikasi in-app (panel bell) + emel.

### 4.3 Perubahan skema data
- `items`: tambah lajur `minimum_quantity` INTEGER NOT NULL DEFAULT 0 (threshold low-stock untuk `getLowStockItems()`).
- Dokumen `password_reset_tokens` (default Laravel) dalam ERD.
- **Invariant `available_quantity`**: `available_quantity = quantity − Σ(loan_items.quantity_loaned − loan_items.quantity_returned)` bagi `loans` berstatus `aktif`/`terlewat`. Dikira semula dalam transaction semasa lulus & pulang.

### 4.4 Metadata
- Isi medan **Pengarang** (kini "-").
- Naik versi setiap dokumen yang disunting + entri changelog jujur yang merujuk spec ini.

## 5. Spesifikasi Perubahan Ikut Dokumen

### 5.1 `01-URS`
- Selaraskan ID FR dengan skema kanonik (§4.1).
- Tambah FR-024 (Lupa Kata Laluan) & kemas FR-017 (Notifikasi in-app).
- Isi metadata; changelog v1.1.

### 5.2 `02-BRS`
- Tukar nilai kos `-` (§6.1) kepada **TBD** eksplisit dengan nota.
- Tambah pautan ke `07-Traceability-Matrix.md` & `10-Project-Plan.md`.
- Changelog v1.1.

### 5.3 `03-SRS`
- Baiki `FR-003` → ikut kanonik; pindah Logout ke `FR-009`.
- Tukar semua URL route ke **plural** (D3): `/user/loan-applications/...`.
- **Tambah spec terperinci FR-017 hingga FR-025** (kini terhenti di FR-016): Notifikasi, QR, Audit, Laporan & Analitik (termasuk jenis laporan), Approval Workflow, Visualisasi, Carian Lanjutan, Lupa/Reset Kata Laluan, Alert Luput/Low-Stock — setiap satu dengan jadual atribut (URL/Method/Controller/Middleware/Fields).
- Tambah **§Concurrency & Transactions** (D4).
- Kemas **State Transition Diagram (§6.1)** ikut handoff (D2); kemas diagram status item.
- **§Security**: tambah polisi kata laluan (min 8, kekuatan) & flow reset.
- Rujuk `items.minimum_quantity`.

### 5.4 `04-ERD`
- Ganti jadual `notifications` dengan **skema native** (§3.1); kemas ringkasan hubungan (morph, bukan `user_id` FK lurus).
- Kemas enum status `loan_applications` & nota pemilikan state (D2).
- Tambah `items.minimum_quantity`.
- Tambah jadual `password_reset_tokens`.
- Dokumen invariant `available_quantity` (§4.3) sebagai nota.
- Tukar gambarajah konsep ASCII → **Mermaid `erDiagram`**.
- Semak semula senarai urutan migration (§5.1) selaras perubahan.

### 5.5 `05-Technical Architecture`
- Route plural (D3) dalam contoh `web.php`.
- **§Notifikasi native**: Notification classes (`ApplicationSubmitted`, `ApplicationApproved`, `ApplicationRejected`, `ReturnReminder`, `LowStockAlert`, `ExpiryAlert`), channel `database`+`mail`, `ShouldQueue`.
- **§Concurrency** (D4).
- Kemas `NotificationService`/Notification classes (+ `sendExpiryAlert`) — selaras wireframe.
- Tambah route/controller password reset.
- **Nota:** `spatie/laravel-permission` belum ada dalam `composer.json` sebenar → tegaskan langkah `composer require` dalam setup.
- Tukar diagram MVC/senibina ASCII → **Mermaid**.

### 5.6 `06-UI/UX`
- Tambah wireframe **Laporan & Analitik**.
- Tambah wireframe/flow **Lupa & Reset Kata Laluan**.
- Tambah wireframe **panel Notifikasi (bell dropdown / senarai)**.
- Baiki jajaran ASCII tersasar (cth. landing page baris ~119-120; status colour box §2.1).
- Changelog v1.1.

## 6. Dokumen Baharu

| Fail | Kandungan |
|------|-----------|
| `00-README.md` | Indeks induk: urutan bacaan, jadual status & versi setiap dokumen, glosari terpusat, gambarajah cara dokumen berkait, ringkasan keputusan D1–D5 |
| `07-Traceability-Matrix.md` | Jadual: BRS Objektif (OBJ-xx) → URS → SRS FR → ID Kes Ujian (TC-xx). Liputan dua hala |
| `08-Test-Plan.md` | Strategi ujian, jenis ujian (unit/feature/UAT), katalog kes ujian per FR, senario UAT, kriteria lulus |
| `09-Decision-Log.md` | ADR D1–D5 + alternatif dipertimbang & rasional |
| `10-Project-Plan.md` | Timeline berfasa (3-4 bulan dari BRS), milestone, deliverable per fasa, dependency |

## 7. Strategi Format / Gambarajah

- **Mermaid** untuk gambarajah kompleks: ERD (`erDiagram`), state machine (`stateDiagram-v2`), aliran proses (`flowchart`/`sequenceDiagram`), senibina.
- **ASCII dikekalkan** untuk wireframe UI (Mermaid tidak sesuai untuk mock-up layout).
- **Caveat didokumen:** Mermaid render dalam VS Code/GitHub tetapi tidak dalam viewer markdown biasa. Diterima kerana dokumen ini rujukan dev.

## 8. Kriteria Penerimaan (Definition of Done)

1. Tiada ID FR yang merujuk perkara berbeza antara URS & SRS; semua FR dalam §4.1 hadir & konsisten.
2. Semua URL route konsisten (plural) merentas SRS & Tech Arch.
3. ERD `notifications` = skema native; `loan_applications`/`loans` enum = D2; `items.minimum_quantity` & `password_reset_tokens` hadir.
4. FR-024 & FR-025 didokumen penuh di URS & SRS; wireframe berkaitan hadir di UI/UX.
5. SRS ada §Concurrency; Tech Arch ada §Notifikasi native + §Concurrency.
6. 5 dokumen baharu (`00`, `07`, `08`, `09`, `10`) wujud & lengkap (tiada TBD tergantung melainkan ditanda sengaja).
7. Gambarajah kompleks ditukar ke Mermaid & render betul; wireframe ASCII kekal & jajaran dibaiki.
8. Setiap dokumen disunting ada metadata pengarang & entri changelog merujuk spec ini.
9. Traceability matrix meliputi setiap OBJ & setiap FR.

## 9. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Renumber FR memecahkan rujukan silang sedia ada | Pra-pembangunan; lakukan satu pas menyeluruh + traceability matrix sahkan liputan |
| Mermaid tak render di sesetengah viewer | Didokumen sebagai caveat; sasaran VS Code/GitHub |
| Skop melebar (banyak dokumen) | Definition of Done jelas (§8); `10-Project-Plan` boleh dijadikan fasa akhir jika masa terhad |
| Ketakselarasan baharu semasa edit | Traceability matrix + pas semakan akhir silang-dokumen |

## 10. Luar Skop

- Penulisan kod aplikasi / migration sebenar.
- Perubahan stack teknologi.
- Migrasi data sebenar.
- Penstrukturan semula fail dokumen sedia ada selain yang dinyatakan.
