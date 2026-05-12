# Sistem Pengurusan Dokumen Kontrak & Bon Pelaksanaan

> **Dokumen Keperluan Produk** (Product Requirements Document)
> Pejabat Setiausaha Kerajaan Negeri Kedah Darul Aman

| Atribut | Nilai |
|---|---|
| **Versi Dokumen** | 1.0 |
| **Tarikh** | 12 Mei 2026 |
| **Status** | Deraf untuk Semakan |
| **Klasifikasi** | Terhad — Kegunaan Dalaman SUK Kedah |
| **Disediakan oleh** | Bahagian Perolehan, SUK Kedah |

---

## Daftar Kandungan

- [Kawalan Dokumen](#kawalan-dokumen)
- [1. Pengenalan](#1-pengenalan)
- [2. Latar Belakang & Konteks Perniagaan](#2-latar-belakang--konteks-perniagaan)
- [3. Gambaran Keseluruhan Sistem](#3-gambaran-keseluruhan-sistem)
- [4. Keperluan Fungsian](#4-keperluan-fungsian)
- [5. Role-Based Access Control (RBAC)](#5-role-based-access-control-rbac)
- [6. Sistem Notifikasi & Alert](#6-sistem-notifikasi--alert)
- [7. Progressive Web Application (PWA)](#7-progressive-web-application-pwa)
- [8. Keperluan Bukan Fungsian](#8-keperluan-bukan-fungsian)
- [9. Senibina Teknikal & Stack](#9-senibina-teknikal--stack)
- [10. Model Data (Database Schema)](#10-model-data-database-schema)
- [11. Integrasi Sistem](#11-integrasi-sistem)
- [12. Reka Bentuk Antaramuka Pengguna](#12-reka-bentuk-antaramuka-pengguna)
- [13. Pelaksanaan & Migrasi](#13-pelaksanaan--migrasi)

---

## Kawalan Dokumen

### Sejarah Versi

| Versi | Tarikh | Disediakan Oleh | Ringkasan Perubahan |
|---|---|---|---|
| 0.1 | 28 April 2026 | Bah. Perolehan | Deraf awal |
| 0.5 | 05 Mei 2026 | Bah. Perolehan | Tambah modul RBAC & Alert |
| 0.9 | 10 Mei 2026 | Bah. Perolehan + Unit ICT | Spesifikasi teknikal Laravel/MySQL |
| 1.0 | 12 Mei 2026 | Bah. Perolehan | Versi awal untuk semakan pengurusan |

### Pengesahan & Kelulusan

| Peranan | Nama | Jawatan | Tarikh |
|---|---|---|---|
| Disediakan oleh | ____________ | Pegawai Perolehan | ________ |
| Disemak oleh | ____________ | Ketua Bahagian Perolehan | ________ |
| Disokong oleh | ____________ | Pegawai Tadbir Tertinggi (ICT) | ________ |
| Diluluskan oleh | ____________ | Setiausaha Kerajaan Negeri | ________ |

### Edaran

- Y.B. Setiausaha Kerajaan Negeri Kedah
- Timbalan Setiausaha Kerajaan (Pengurusan)
- Pengarah Bahagian Perolehan
- Pengarah Unit Teknologi Maklumat
- Pengarah Bahagian Kewangan
- Pegawai Audit Dalam
- Penasihat Undang-Undang Negeri
- Vendor / Pembekal Pembangunan Sistem (selepas tender)

---

## 1. Pengenalan

### 1.1 Tujuan Dokumen

Dokumen Keperluan Produk (PRD) ini menggariskan keperluan fungsian dan bukan fungsian bagi pembangunan **Sistem Pengurusan Dokumen Kontrak & Bon Pelaksanaan** untuk Pejabat Setiausaha Kerajaan Negeri (SUK) Kedah. Dokumen ini berfungsi sebagai rujukan tunggal kepada pihak pembangun sistem, pasukan kawalan kualiti, pengguna akhir dan pihak pengurusan dalam proses pelaksanaan projek.

Skop dokumen ini meliputi keperluan perniagaan, spesifikasi teknikal, senibina sistem, modul fungsian, peraturan kawalan akses (RBAC), enjin notifikasi serta keperluan Progressive Web Application (PWA) untuk peranti mudah alih.

### 1.2 Latar Belakang Projek

Bahagian Perolehan SUK Kedah menguruskan **lebih daripada 170 kontrak aktif** pada bila-bila masa, melibatkan nilai keseluruhan **melebihi RM 18 juta**. Pemantauan dokumen kontrak selepas Surat Setuju Terima (SST) dikeluarkan dilakukan secara manual menggunakan helaian Excel dan pemerhatian individu. Pendekatan ini telah membawa kepada beberapa isu kritikal:

- **Dokumen kontrak terlepas pandang** selepas perolehan dilaksanakan, menyebabkan kontrak formal tidak disempurnakan dalam tempoh yang ditetapkan.
- **Pegawai Bertanggungjawab (PIC) tidak menerima amaran proaktif** berkenaan tarikh penting seperti tarikh tamat bon pelaksanaan, deadline penghantaran deraf kontrak ke Penasihat Undang-undang (PUU) dan penilaian prestasi pembekal.
- **Teguran audit berulang** berkaitan bon pelaksanaan yang tidak dipantau, bon yang tidak diserah balik selepas tamat kontrak dan dokumen kontrak yang lewat disempurnakan.
- **Ketiadaan visibiliti pengurusan** terhadap portfolio kontrak secara keseluruhan, menyukarkan pengambilan keputusan dan perancangan strategik.

### 1.3 Objektif Sistem

1. Menyediakan satu platform berpusat untuk pendaftaran, pemantauan dan pengurusan kitar hayat penuh kontrak dari SST hingga ke penyerahan balik bon pelaksanaan.
2. Mengautomasikan sistem amaran (alert) proaktif kepada PIC dan pihak berkaitan berdasarkan peraturan perniagaan yang ditetapkan.
3. Mengurangkan teguran audit melalui penjejakan automatik tarikh-tarikh kritikal seperti tarikh tamat bon, tarikh deraf kontrak ke PUU dan tarikh stamping.
4. Menyediakan paparan eksekutif (dashboard) untuk pengurusan atasan memantau prestasi dan risiko portfolio kontrak secara real-time.
5. Memudahkan integrasi dengan sistem sedia ada seperti iDaftar, ePerolehan, ATS dan EPSM bagi mengelakkan kemasukan data berulang.
6. Menyediakan capaian melalui peranti mudah alih (PWA) untuk pegawai dan pengurus mengakses sistem di mana sahaja.

### 1.4 Skop Sistem

#### 1.4.1 Dalam Skop

- Pendaftaran dan pengurusan Surat Setuju Terima (SST) dan Lantikan.
- Penjejakan kitar hayat dokumen kontrak (deraf, semakan PUU, tandatangan, stamping).
- Pengurusan bon pelaksanaan (insurans, jaminan bank) dan tarikh tamat.
- Penilaian prestasi pembekal secara berkala.
- Sistem notifikasi pelbagai saluran (e-mel, in-app, push).
- Role-Based Access Control (RBAC) dengan **7 peranan** terbina dalam.
- Dashboard eksekutif dengan visualisasi Gantt chart, peta haba dan funnel.
- Audit trail terperinci untuk semua transaksi.
- Penjanaan laporan standard dan tersuai.
- PWA untuk capaian melalui iOS dan Android.

#### 1.4.2 Luar Skop

- Modul perolehan akhirnya (e-tender / e-sebut harga) — telah dikendalikan oleh sistem ePerolehan.
- Pengurusan kewangan dan pembayaran terus kepada pembekal — dikendalikan oleh sistem GFMAS / iSPEKS.
- Pengurusan rekod fizikal pembekal (akan dirujuk kepada iDaftar melalui API).
- Pengurusan inventori aset hasil perolehan.
- Fungsi e-perundangan PUU (skop sistem PUU tersendiri).

### 1.5 Definisi, Akronim & Singkatan

| Istilah | Definisi |
|---|---|
| SUK | Pejabat Setiausaha Kerajaan Negeri |
| SST | Surat Setuju Terima — surat rasmi penerimaan tawaran perolehan |
| PIC | Person In Charge — Pegawai Bertanggungjawab |
| PUU | Penasihat Undang-undang Negeri |
| ATS | Aplikasi Tahap Sistem (sistem perolehan kerajaan) |
| EPSM | Sistem Pengurusan Pegawai EPSM Kedah |
| RBAC | Role-Based Access Control — kawalan capaian berasaskan peranan |
| PWA | Progressive Web Application — aplikasi web boleh dipasang seperti app |
| FCM | Firebase Cloud Messaging — perkhidmatan push notification merentas platform |
| APNS | Apple Push Notification Service |
| MVP | Minimum Viable Product |
| UAT | User Acceptance Testing |
| KPI | Key Performance Indicator |
| Kategori 1 | Kontrak: SST dikeluarkan, deraf belum ke PUU, kontrak tamat dalam 6 bulan |
| Kategori 2 | Kontrak: SST dikeluarkan, dokumen belum ke PUU melebihi 4 bulan |
| Bon Pelaksanaan | Jaminan bank/insurans wajib untuk kontrak melebihi RM 200,000 |
| Kontrak Formal | Kontrak bertulis ditandatangani rasmi, biasanya tempoh siap > 4 bulan |

### 1.6 Rujukan

- Surat Pekeliling Perbendaharaan Bil. 5 Tahun 2007 — Tatacara Pengurusan Perolehan Kerajaan
- Akta Acara Kewangan 1957
- Pekeliling Perbendaharaan PK 1/2013 — Tatacara Pengurusan Kontrak
- Garis Panduan Pengurusan Aset Kerajaan
- Polisi Keselamatan ICT Kerajaan Negeri Kedah
- ISMS Negeri Kedah — ISO/IEC 27001:2022
- Akta Perlindungan Data Peribadi 2010 (Akta 709)

---

## 2. Latar Belakang & Konteks Perniagaan

### 2.1 Pernyataan Masalah

Analisis terhadap proses pengurusan kontrak sedia ada di SUK Kedah telah mengenal pasti lima isu kritikal yang menyumbang secara langsung kepada teguran audit berulang dan ketidakcekapan operasi:

#### 2.1.1 Kategori 1 — Kontrak Tanpa Dokumen Formal

Kontrak yang SST telah dikeluarkan tetapi deraf kontrak belum dihantar kepada PUU, sementara tarikh tamat kontrak akan tiba dalam tempoh 6 bulan. Pada masa ini, terdapat **4 kontrak Kategori 1** yang aktif.
**Risiko:** kontrak tamat tanpa dokumen formal yang sah, mendedahkan kerajaan kepada risiko undang-undang.

#### 2.1.2 Kategori 2 — Dokumen Tertangguh Melebihi 4 Bulan

Kontrak yang dokumen kontraknya belum disubmit ke PUU melebihi 4 bulan dari tarikh SST dikeluarkan. Pada masa ini terdapat **7 kontrak Kategori 2**.
**Risiko:** kegagalan mematuhi keperluan pekeliling perbendaharaan.

#### 2.1.3 Bon Pelaksanaan Tidak Dipantau

Tarikh tamat bon pelaksanaan tidak dipantau secara sistematik. Pegawai sering terlepas tarikh kritikal seperti deadline pengeluaran notis kepada pembekal apabila tarikh tamat bon tidak sama dengan tarikh tamat kontrak.

#### 2.1.4 Bon Tidak Diserah Balik Selepas Kontrak Tamat

Selepas kontrak tamat, bon pelaksanaan perlu diserah balik kepada pembekal (jika tiada tuntutan) atau dilepaskan ke dalam simpanan jabatan. Tiada sistem peringatan automatik menyebabkan 3 bon belum diserah balik pada masa ini, dengan tertangguh sehingga 128 hari.

#### 2.1.5 Penilaian Prestasi Pembekal Tidak Konsisten

Laporan Bulanan Bahagian B yang memerlukan penilaian prestasi pembekal selalunya tidak dikemaskini secara berkala. Bagi kontrak formal yang membenarkan lebih dari satu penilaian, tiada mekanisme automatik untuk memberi peringatan kepada PIC.

### 2.2 Pengguna Sasaran (Target Users)

| Pengguna | Bilangan Anggaran | Penggunaan Utama |
|---|---|---|
| Setiausaha Kerajaan & Timbalan | 2 - 3 | Paparan eksekutif, laporan strategik |
| Pengarah Bahagian | 8 - 12 | Pemantauan jabatan, kelulusan |
| Ketua Unit / Seksyen | 25 - 35 | Pemantauan unit, kelulusan penilaian, kelulusan kontrak formal |
| Pegawai Perolehan (PIC) | 60 - 80 | Pendaftaran SST, pengemaskinian harian |
| Pegawai Audit Dalam | 3 - 5 | Capaian baca-sahaja, audit trail |
| Pentadbir Sistem | 2 - 3 | Pengurusan pengguna, master data |
| **JUMLAH ANGGARAN PENGGUNA AKTIF** | **100 - 138** | — |

> **Nota:** Pegawai PUU, Pegawai Kewangan dan Urusetia Perolehan tidak diberikan akaun sistem dalam fasa ini. Proses berkaitan PUU (semakan deraf kontrak) dan kewangan (pembayaran) terus dikendalikan di luar sistem ini. Maklumat tarikh-tarikh berkaitan (Tarikh Deraf ke PUU, dll.) diisi oleh PIC sebagai data sahaja.

### 2.3 Faedah yang Dijangka

#### 2.3.1 Faedah Kuantitatif

- **Pengurangan teguran audit** berkaitan kontrak dan bon sekurang-kurangnya 80% dalam tempoh 12 bulan selepas pelaksanaan.
- **Penjimatan masa pemprosesan** sebanyak 60% melalui pendaftaran automatik dari API iDaftar dan ePerolehan (tiada lagi key-in data berulang).
- **Peningkatan pematuhan tarikh kritikal** kepada minimum 95% melalui alert proaktif.
- **Pengurangan bilangan kontrak Kategori 1 & 2** kepada sifar dalam tempoh 6 bulan.

#### 2.3.2 Faedah Kualitatif

- Peningkatan ketelusan dalam proses pengurusan kontrak.
- Penambahbaikan pengalaman pegawai dengan akses mudah alih melalui PWA.
- Penyatuan rekod kontrak — *single source of truth*.
- Peningkatan keupayaan pengambilan keputusan eksekutif melalui paparan real-time.

### 2.4 Andaian & Kekangan

#### 2.4.1 Andaian

- Pengguna sistem mempunyai akses kepada peranti dengan pelayar moden (Chrome 90+, Safari 14+, Edge 90+).
- Sambungan internet di pejabat dan capaian mudah alih adalah memuaskan.
- API integrasi (iDaftar, ePerolehan, ATS, EPSM) menyediakan dokumentasi dan sokongan teknikal.
- Penyelenggaraan dan operasi (DevOps) akan dikendalikan oleh Unit ICT SUK atau vendor pelaksana.

#### 2.4.2 Kekangan

- Sistem mesti mematuhi Polisi Keselamatan ICT Kerajaan Negeri Kedah.
- Data peribadi pengguna dan pembekal mesti dikendalikan mengikut Akta Perlindungan Data Peribadi 2010.
- Pelayan sistem mesti dihos di pusat data Kerajaan Negeri Kedah (on-premise) atau cloud yang disetujui (sovereign cloud).
- Bahasa antara muka utama adalah Bahasa Malaysia, dengan istilah teknikal dalam Bahasa Inggeris.

---

## 3. Gambaran Keseluruhan Sistem

### 3.1 Senibina Logikal (High-Level Architecture)

Sistem ini dibangunkan menggunakan **senibina tiga lapisan (3-tier architecture)** dengan komponen tambahan untuk PWA, sistem notifikasi dan integrasi pihak ketiga.

#### 3.1.1 Lapisan Persembahan (Presentation Layer)

- **Admin Panel** — Antaramuka pentadbiran berasaskan Laravel FilamentPHP v3.x untuk pengurusan sistem, master data, dan operasi harian.
- **PWA** — Aplikasi web progresif untuk capaian melalui iOS dan Android. Boleh dipasang ke skrin utama dan menyokong push notification.
- **API REST** — Endpoint untuk integrasi dan kegunaan masa hadapan (contoh: app native).

#### 3.1.2 Lapisan Aplikasi (Application Layer)

- **Laravel Framework 11.x** — Framework utama PHP dengan sokongan LTS sehingga tahun 2027.
- **FilamentPHP 3.x** — Admin panel framework dengan UI/UX moden, built-in CRUD, form builder, table builder, dashboard widgets, dan notifications.
- **Queue Worker** — Memproses kerja latar belakang seperti penghantaran e-mel dan push notification.
- **Scheduler** — Menjalankan kerja berjadual seperti semakan harian peraturan alert.
- **Cache Layer** — Redis untuk session, cache pertanyaan dan queue.

#### 3.1.3 Lapisan Data (Data Layer)

- **MySQL 8.0** — Pangkalan data utama untuk data transaksi.
- **Redis 7.x** — Cache, session store dan queue backend.
- **Storage S3-Compatible** — Penyimpanan lampiran dokumen (PDF, gambar).

#### 3.1.4 Komponen Integrasi

- **API Gateway** — Mengurus capaian ke API luaran (iDaftar, ePerolehan, ATS, EPSM).
- **SMTP Server / Email Service** — Penghantaran e-mel notifikasi.
- **Firebase Cloud Messaging (FCM)** — Push notification untuk PWA merentas iOS dan Android.

### 3.2 Diagram Komponen Sistem

```
┌─────────────────────────────────────────────────────────────┐
│   LAPISAN PERSEMBAHAN (Presentation)                        │
│   ┌──────────┐  ┌──────────┐  ┌──────────────────────┐     │
│   │ Filament │  │   PWA    │  │  API REST (mobile)   │     │
│   │ Admin    │  │ iOS/And. │  │  Laravel Sanctum     │     │
│   │ Panel    │  │ Service  │  │  Auth                │     │
│   └────┬─────┘  └────┬─────┘  └──────────┬───────────┘     │
└────────┼─────────────┼──────────────────┼─────────────────┘
         ↓             ↓                  ↓
┌─────────────────────────────────────────────────────────────┐
│   LAPISAN APLIKASI · Laravel 11 + PHP 8.2 + Filament 3.x    │
│                                                              │
│   ┌──────────┐  ┌──────────┐  ┌──────────────────────┐     │
│   │ HTTP     │  │ Queue    │  │  Scheduler           │     │
│   │ Routes/  │  │ Worker   │  │  (Cron-based)        │     │
│   │ Filament │  │ (Redis)  │  │  - Alert daily       │     │
│   └──────────┘  └──────────┘  └──────────────────────┘     │
│                                                              │
│   Modul: Auth | RBAC | SST | Kontrak | Bon | Notifikasi     │
└─────────────────┬──────────────────────────┬────────────────┘
                  ↓                          ↓
┌─────────────────────────────────┐  ┌──────────────────────┐
│   LAPISAN DATA                  │  │  INTEGRASI LUARAN    │
│   ┌─────────┐ ┌─────────┐       │  │  - API iDaftar       │
│   │ MySQL 8 │ │ Redis 7 │       │  │  - API ePerolehan    │
│   └─────────┘ └─────────┘       │  │  - API ATS           │
│   ┌────────────────┐            │  │  - API EPSM          │
│   │ S3 Storage     │            │  │  - SMTP / FCM        │
│   │ (Lampiran PDF) │            │  │                      │
│   └────────────────┘            │  │                      │
└─────────────────────────────────┘  └──────────────────────┘
```

### 3.3 Modul Sistem (Functional Modules)

| No. | Modul | Penerangan Ringkas |
|---|---|---|
| M1 | Authentication & RBAC | Log masuk, pengurusan peranan dan kebenaran |
| M2 | Daftar SST / Lantikan | Pendaftaran Surat Setuju Terima |
| M3 | Daftar Kontrak | Penjejakan dokumen kontrak ke PUU, tandatangan, stamping |
| M4 | Bon Pelaksanaan & Insurans | Pengurusan jaminan bank / insurans |
| M5 | Penilaian Prestasi | Penilaian pembekal Laporan Bulanan B |
| M6 | Dashboard & Laporan | Paparan eksekutif dan janaan laporan |
| M7 | Sistem Notifikasi | Enjin alert pelbagai saluran |
| M8 | Audit Trail | Log aktiviti semua transaksi |
| M9 | Utiliti & Master Data | Pengurusan jabatan, seksyen, kod rujukan |

---

## 4. Keperluan Fungsian

Setiap keperluan diberi ID unik (`FR-Mx-yyy`) untuk rujukan dalam fasa pembangunan dan UAT.

### 4.1 M1 — Modul Authentication & RBAC

#### 4.1.1 Pendaftaran & Log Masuk

| ID | Keperluan | Keutamaan |
|---|---|---|
| FR-M1-001 | Sistem mesti membenarkan pendaftaran pengguna baharu melalui kemasukan No. Kad Pengenalan, dengan butiran lain (nama, jabatan, jawatan, e-mel) diisi secara automatik melalui API EPSM. | Wajib |
| FR-M1-002 | Pengguna mesti mengaktifkan akaun melalui pengesahan e-mel rasmi sebelum boleh log masuk. | Wajib |
| FR-M1-003 | Sistem mesti menyokong log masuk dengan e-mel + kata laluan, dengan kekuatan kata laluan minimum 8 aksara (huruf besar, huruf kecil, nombor, simbol). | Wajib |
| FR-M1-004 | Sistem mesti menyediakan Two-Factor Authentication (2FA) melalui aplikasi authenticator (TOTP) atau e-mel. | Wajib |
| FR-M1-005 | Sistem mesti mengunci akaun selepas 5 percubaan log masuk gagal berturut-turut. | Wajib |
| FR-M1-006 | Sistem mesti memaksa pengguna menukar kata laluan setiap 90 hari. | Wajib |
| FR-M1-007 | Sistem mesti menyediakan fungsi 'Lupa Kata Laluan' melalui pautan unik yang dihantar ke e-mel rasmi (sah selama 60 minit). | Wajib |
| FR-M1-008 | Sesi pengguna mesti tamat secara automatik selepas 30 minit tidak aktif. | Wajib |
| FR-M1-009 | Sistem mesti merekodkan semua aktiviti log masuk (berjaya & gagal) dalam audit trail. | Wajib |
| FR-M1-010 | Sistem mesti menyokong Single Sign-On (SSO) melalui MyGovUC / MAMPU IAM bagi fasa kedua. | Boleh Tambah |

#### 4.1.2 Pengurusan Peranan & Kebenaran

| ID | Keperluan | Keutamaan |
|---|---|---|
| FR-M1-011 | Sistem mesti menyokong sekurang-kurangnya 7 peranan terbina dalam (built-in roles) dengan kebenaran berbeza. | Wajib |
| FR-M1-012 | Pentadbir Sistem mesti boleh mencipta peranan tersuai (custom roles) dengan kombinasi kebenaran yang ditetapkan. | Wajib |
| FR-M1-013 | Setiap pengguna boleh mempunyai satu atau lebih peranan secara serentak. | Wajib |
| FR-M1-014 | Kebenaran (permissions) mesti dikuatkuasakan pada peringkat tindakan (CRUD) bagi setiap modul. | Wajib |
| FR-M1-015 | Capaian data mesti dihadkan berdasarkan jabatan dan unit pengguna (row-level security). | Wajib |
| FR-M1-016 | Sistem mesti menyediakan paparan matriks kebenaran (permission matrix) untuk semakan pengurusan. | Wajib |
| FR-M1-017 | Perubahan peranan pengguna mesti memerlukan kelulusan oleh Pentadbir Sistem dan direkodkan dalam audit trail. | Wajib |

### 4.2 M2 — Modul Daftar SST / Lantikan

Modul ini menguruskan pendaftaran Surat Setuju Terima yang merupakan titik permulaan kitar hayat kontrak.

| ID | Keperluan | Keutamaan |
|---|---|---|
| FR-M2-001 | Sistem mesti menyediakan borang pendaftaran SST dengan medan-medan: No. Rujukan SST, Tarikh SST, Seksyen/Unit, PIC Projek, Nama Pembekal, No. Pendaftaran Pembekal, PIC Pembekal, No. Telefon PIC, E-mel PIC, Skop Pembekal/Kontraktor (Bekalan/Perkhidmatan/Kerja), Kaedah Perolehan, Tajuk Perjanjian, No. Perolehan, No. LO, Tarikh LO, No. Invois, Tarikh Mula Pesanan, Tarikh Terima Pesanan, Nilai Kontrak, Tempoh Kontrak. | Wajib |
| FR-M2-002 | Maklumat Nama Pembekal & No. Pendaftaran mesti diambil secara automatik dari API iDaftar berdasarkan kemasukan No. Pendaftaran. | Wajib |
| FR-M2-003 | Maklumat Tajuk Perjanjian, No. Perolehan dan Nilai Kontrak mesti boleh diambil dari API ePerolehan / ATS. | Wajib |
| FR-M2-004 | Sistem mesti menyediakan dropdown untuk Seksyen/Unit yang dipopulasikan dari jadual master `kod_seksyen`. | Wajib |
| FR-M2-005 | Kaedah Perolehan mesti dropdown dengan pilihan: Pembelian Terus, Lantikan Terus, Tender, Sebut Harga, Rundingan Terus. | Wajib |
| FR-M2-006 | Sistem mesti membenarkan kemaskini Tarikh Lanjutan 1 dan Tarikh Lanjutan 2 jika kontrak dilanjutkan. | Wajib |
| FR-M2-007 | Sistem mesti merekod Pegawai yang menandatangani Surat dan Tarikh Tandatangan Kontrak. | Wajib |
| FR-M2-008 | Sistem mesti membenarkan PIC mengisi maklumat Penalti/Denda mengikut klausa kontrak. | Wajib |
| FR-M2-009 | Sistem mesti membenarkan Ketua Bahagian Perolehan / Pengarah menetapkan status 'Kontrak Formal' (Ya/Tidak). Lalai: Ya jika tempoh siap melebihi 4 bulan. | Wajib |
| FR-M2-010 | Sistem mesti menyediakan fungsi senarai, carian, tapis (filter), kemaskini dan padam (soft delete) bagi rekod SST. | Wajib |
| FR-M2-011 | Sistem mesti membenarkan muat naik lampiran dokumen (PDF, JPG, PNG) dengan saiz maksimum 10MB setiap fail. | Wajib |

### 4.3 M3 — Modul Daftar Kontrak

Modul ini menjejaki proses penyediaan dokumen kontrak formal dari deraf sehingga stamping.

| ID | Keperluan | Keutamaan |
|---|---|---|
| FR-M3-001 | Sistem mesti menyediakan borang daftar kontrak dengan medan: Nama Kontrak (dari ATS), Tarikh SST, Tarikh Mula Perjanjian, Tarikh Tamat Perjanjian, Tempoh Kontrak. | Wajib |
| FR-M3-002 | Sistem mesti merekod Tarikh Deraf Kontrak Dihantar ke PUU, Tarikh Deraf Dihantar ke Kontraktor, Tarikh Tandatangan Kontrak dan Tarikh Stamping. | Wajib |
| FR-M3-003 | Sistem mesti menentukan secara automatik kategori kontrak berdasarkan peraturan:<br>• **Kategori 1**: SST dikeluarkan, deraf belum dihantar ke PUU, kontrak akan tamat dalam 6 bulan.<br>• **Kategori 2**: SST dikeluarkan, dokumen kontrak belum dihantar ke PUU melebihi 4 bulan dari tarikh SST. | Wajib |
| FR-M3-004 | Kontrak dikira siap sempurna apabila Tarikh Stamping telah direkodkan. | Wajib |
| FR-M3-005 | Sistem mesti membenarkan tracking flow dengan beberapa kemasukan tarikh kemaskini status. | Wajib |
| FR-M3-006 | Apabila penjanaan laporan, sistem mesti mengambil status terkini (latest keyin) dari rekod status. | Wajib |
| FR-M3-007 | PIC mesti boleh menambah catatan / nota dalaman pada setiap perubahan status. | Wajib |

### 4.4 M4 — Modul Bon Pelaksanaan & Insurans

| ID | Keperluan | Keutamaan |
|---|---|---|
| FR-M4-001 | Sistem mesti merekod butiran bon pelaksanaan: No. Rujukan Bon, Nilai Bon (RM), Pengeluar Bon, Tempoh Bon Pelaksanaan (Tarikh Mula - Tamat), Status Bon, Tarikh Serah Balik Bon. | Wajib |
| FR-M4-002 | Sistem mesti merekod butiran insurans (sebagai alternatif kepada bon): Nilai Insurans, Pengeluar Insurans, Tempoh Insurans (Tarikh Dari - Hingga). | Wajib |
| FR-M4-003 | Sistem mesti menguatkuasa peraturan: Bon ATAU Insurans diisi (tidak kedua-duanya). | Wajib |
| FR-M4-004 | Sistem mesti menguatkuasa peraturan: Kontrak melebihi RM 200,000 wajib mempunyai bon pelaksanaan. | Wajib |
| FR-M4-005 | Sistem mesti membandingkan tarikh tamat bon dengan tarikh tamat kontrak. Jika tarikh tamat bon < tarikh tamat kontrak, sistem mesti menjana notis automatik kepada pembekal. | Wajib |
| FR-M4-006 | Status Bon mesti boleh ditetapkan kepada: Aktif, Akan Tamat (≤ 6 bulan), Tamat, Serah Balik Pembekal, Dalam Simpanan Jabatan. | Wajib |
| FR-M4-007 | Sistem mesti menjana amaran 180, 90, 30 dan 7 hari sebelum tarikh tamat bon (lihat M7). | Wajib |
| FR-M4-008 | Selepas kontrak tamat, sistem mesti memberi peringatan untuk serah balik bon dalam tempoh 30 hari. | Wajib |

### 4.5 M5 — Modul Penilaian Prestasi

| ID | Keperluan | Keutamaan |
|---|---|---|
| FR-M5-001 | Sistem mesti menyenaraikan SST yang aktif berdasarkan PIC yang log masuk (filter mengikut unit). | Wajib |
| FR-M5-002 | Sistem mesti menyediakan borang penilaian dengan kriteria standard Laporan Bulanan Bahagian B. | Wajib |
| FR-M5-003 | Bagi kontrak formal, sistem mesti membenarkan lebih dari satu penilaian (penilaian berulang setiap bulan). | Wajib |
| FR-M5-004 | Sistem mesti menjana PDF penilaian dengan logo dan format rasmi SUK Kedah. | Wajib |
| FR-M5-005 | Sistem mesti menyediakan analisis penilaian prestasi keseluruhan pembekal (skor min, trend, ranking). | Wajib |
| FR-M5-006 | Penilaian yang menunjukkan skor di bawah 60% berturut-turut 2 bulan mesti dieskalasi kepada Pengarah Bahagian. | Wajib |

### 4.6 M6 — Modul Dashboard & Laporan

#### 4.6.1 Dashboard Eksekutif

Dashboard mesti memaparkan visualisasi berikut dengan kemaskini secara real-time:

- **Kad Penunjuk Utama (KPI Cards):** Bilangan kontrak aktif, kontrak tamat, dokumen belum siap, bon aktif, bon akan tamat, bon belum serah.
- **Carta Gantt Portfolio Kontrak:** Lini masa visual semua kontrak dengan penanda tarikh kritikal.
- **Kalendar Risiko:** Peta haba bulanan menunjukkan kepadatan acara kritikal.
- **Funnel Kitar Hayat:** Bilangan kontrak pada setiap peringkat (SST → PUU → Sign → Stamping → Aktif).
- **Donut Status:** Taburan status portfolio (sihat, dokumen belum siap, tamat sempurna, bon belum serah).
- **Peta Haba Jabatan:** Prestasi mengikut jabatan dengan pengkodan warna.
- **Trend 12 Bulan:** SST baharu vs kontrak siap vs kontrak tertangguh.
- **Aktiviti Terkini (Activity Feed):** Audit trail live feed.

#### 4.6.2 Laporan Standard

| ID | Laporan | Format |
|---|---|---|
| RPT-001 | Laporan Bulanan Pengurusan Kontrak (untuk YB SUK) | PDF / Excel |
| RPT-002 | Laporan Bon Pelaksanaan (Aktif, Akan Tamat, Belum Serah) | PDF / Excel |
| RPT-003 | Laporan Kontrak Kategori 1 & Kategori 2 | PDF / Excel |
| RPT-004 | Laporan Prestasi Pembekal (per bulan, per pembekal) | PDF / Excel |
| RPT-005 | Laporan Audit Trail (mengikut tempoh dan pengguna) | PDF / Excel |
| RPT-006 | Laporan Tahunan Pengurusan Kontrak | PDF |
| RPT-007 | Laporan untuk JKR / Ketua Audit Negara (formula standard) | PDF |

#### 4.6.3 Eksport Data

- Sistem mesti menyokong eksport data ke format Excel (`.xlsx`) dan CSV.
- Sistem mesti menyokong cetakan dan eksport PDF dengan format A4 dan letterhead rasmi SUK Kedah.
- Eksport mesti menghormati kebenaran pengguna — data hanya yang boleh diakses.

---

## 5. Role-Based Access Control (RBAC)

Bahagian ini menerangkan secara terperinci sistem kawalan capaian berasaskan peranan yang akan dilaksanakan menggunakan package **Laravel Spatie/Permission**. RBAC adalah lapisan kawalan keselamatan utama yang menentukan apa yang setiap pengguna boleh lihat dan lakukan dalam sistem.

### 5.1 Prinsip RBAC

1. Setiap pengguna mempunyai satu atau lebih peranan (role).
2. Setiap peranan terdiri daripada satu atau lebih kebenaran (permission).
3. Kebenaran ditentukan pada peringkat tindakan terhadap sumber, dengan format ringkas: `<sumber>.<tindakan>` contoh: `sst.create`, `kontrak.approve`, `bon.delete`.
4. Capaian data dihadkan oleh peraturan scoping mengikut jabatan dan unit pengguna (row-level security).
5. Perubahan peranan direkodkan dalam audit trail dan memerlukan kelulusan Pentadbir Sistem.

### 5.2 Senarai Peranan (Roles)

Sistem akan menyediakan **7 peranan** terbina dalam:

| Slug | Nama Peranan | Penerangan |
|---|---|---|
| `super-admin` | Super Administrator | Capaian penuh sistem, termasuk konfigurasi dan debug. Hanya 1-2 pengguna. |
| `admin` | Pentadbir Sistem | Pengurusan pengguna, peranan dan master data. Tidak boleh mengubah data kontrak. |
| `sk-exec` | Eksekutif (SK & TSK) | Capaian baca-sahaja kepada semua data merentas jabatan. Dashboard eksekutif penuh. |
| `pengarah` | Pengarah Bahagian | Capaian penuh ke semua kontrak dalam jabatan sendiri. Boleh meluluskan kontrak bernilai tinggi dan menetapkan status Kontrak Formal. |
| `ketua-unit` | Ketua Unit / Seksyen | Capaian ke kontrak unit sendiri. Boleh meluluskan penilaian prestasi dan kontrak formal peringkat unit. |
| `pic` | Pegawai Perolehan (PIC) | Mendaftar SST, mengemaskini data kontrak, mengisi penilaian prestasi. **Penerima alert utama.** |
| `audit` | Pegawai Audit Dalam | Capaian baca-sahaja kepada semua data dan audit trail. Boleh menjana laporan audit. |

> **Nota:** Urusetia Perolehan, PUU dan Pegawai Kewangan tidak diberi akaun sistem dalam fasa ini. Peranan mereka dalam proses perniagaan tetap wujud (semakan deraf, kelulusan, pembayaran) tetapi dikendalikan di luar sistem. PIC akan merekod tarikh-tarikh berkaitan (cth: Tarikh Deraf ke PUU) sebagai data.

### 5.3 Matriks Kebenaran (Permission Matrix)

Petunjuk: **F** = Full Access (CRUD), **C** = Create, **R** = Read, **U** = Update, **D** = Delete, **A** = Approve, **−** = No Access.

| Modul | Super | Admin | Exec | Pengarah | K.Unit | PIC | Audit |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| Pengguna | F | F | R | R | R | − | R |
| Peranan & Permission | F | F | R | R | − | − | R |
| Daftar SST | F | F | R | F | F | CRU | R |
| Daftar Kontrak | F | F | R | F+A | F | CRU | R |
| Bon Pelaksanaan | F | F | R | F | F | CRU | R |
| Penilaian Prestasi | F | R | R | RA | RA | CRU | R |
| Dashboard | F | R | F | F | R | R | R |
| Laporan | F | F | F | F | R | R | F |
| Notifikasi (own) | F | F | F | F | F | F | F |
| Audit Trail | F | R | R | R | − | − | F |
| Master Data | F | F | R | − | − | − | R |
| Konfigurasi Sistem | F | R | − | − | − | − | − |

#### 5.3.1 Nota Tambahan Permission Matrix

- Capaian 'R' bagi peranan Eksekutif (sk-exec), Pengarah, Audit dan Ketua Unit adalah dengan **SCOPING**: hanya data dalam jabatan / unit mereka sendiri (kecuali Eksekutif dan Audit yang dibenarkan merentas).
- Tindakan 'Approve' (A) bagi modul Kontrak hanya terpakai kepada kontrak bernilai > **RM 500,000**.
- Tindakan 'Delete' (D) adalah soft delete sahaja. Hard delete hanya boleh dilakukan oleh super-admin selepas tempoh 90 hari.
- Penilaian Prestasi: PIC mencipta penilaian, Ketua Unit meluluskan. Pengarah boleh menolak / minta semakan.
- Modul Master Data hanya boleh diubah oleh `admin` dan `super-admin`.

### 5.4 Scoping Data (Row-Level Security)

| Peranan | Scope Data |
|---|---|
| `super-admin`, `admin`, `audit`, `sk-exec` | Semua data merentas semua jabatan |
| `pengarah` | Semua data dalam jabatan sendiri sahaja |
| `ketua-unit` | Semua data dalam unit / seksyen sendiri sahaja |
| `pic` | Hanya data SST / kontrak yang didaftarkan oleh diri sendiri (kecuali jika diberi capaian tambahan oleh Ketua Unit) |

### 5.5 Aliran Kelulusan (Approval Flow)

#### 5.5.1 Pendaftaran Pengguna Baharu

```
PIC mencipta akaun
   → Pentadbir Sistem semak
      → Aktifkan / Tolak
         → Notifikasi automatik kepada pengguna
```

#### 5.5.2 Penetapan Peranan

```
Admin pilih peranan untuk pengguna
   → Sistem hantar notifikasi pengesahan kepada Pengarah Bahagian pengguna
      → Pengarah lulus / tolak
         → Audit trail direkodkan
```

#### 5.5.3 Penilaian Prestasi

```
PIC isi penilaian
   → Ketua Unit semak
      → Lulus / Minta semakan
         → Notifikasi automatik
            → PDF dijana selepas lulus
```

#### 5.5.4 Kontrak Bernilai Tinggi (> RM 500,000)

```
PIC daftar
   → Ketua Unit semak
      → Pengarah Bahagian lulus
         → Notifikasi kepada Setiausaha Kerajaan
```

---

## 6. Sistem Notifikasi & Alert

Sistem notifikasi adalah komponen kritikal yang menyelesaikan masalah utama *"PIC tidak alert"* yang menyebabkan dokumen kontrak terlepas pandang.

### 6.1 Saluran Notifikasi

| Saluran | Penggunaan | Kebergantungan Teknikal |
|---|---|---|
| **E-mel** | Saluran utama untuk semua jenis notifikasi | SMTP server / Mailgun / SendGrid |
| **In-App (Database)** | Notifikasi dalam aplikasi (icon loceng) | Laravel Notifications |
| **Push Notification** | PWA push untuk peranti mudah alih — kritikal untuk peringatan segera | Firebase Cloud Messaging (FCM) |
| **WhatsApp** | Pilihan tambahan (fasa 2) | WhatsApp Business API |

> **Nota:** SMS sengaja tidak disertakan dalam fasa ini. Untuk amaran kritikal, gabungan **e-mel + push notification PWA** (yang akan muncul pada peranti mudah alih PIC dengan bunyi dan getaran) sudah memadai. Ini juga mengurangkan kos operasi berulang (RM 0.05-0.15 per SMS) dan kompleksiti integrasi dengan SMS gateway.

### 6.2 Peraturan Alert (Alert Triggers)

Setiap peraturan alert dijalankan oleh Scheduler Laravel pada waktu yang ditetapkan, biasanya **8:00 pagi setiap hari**.

#### 6.2.1 Alert Berkaitan Kategori Kontrak

| ID | Trigger | Syarat | Penerima | Saluran |
|---|---|---|---|---|
| ALR-001 | Kategori 1 Baharu | SST wujud, tiada deraf ke PUU, tarikh tamat kontrak − hari ini ≤ 180 hari | PIC + cc Ketua Unit | E-mel + Push |
| ALR-002 | Kategori 1 Berulang | Kategori 1 masih wujud selepas 7 hari | PIC + Ketua Unit + Pengarah Bahagian | E-mel |
| ALR-003 | Kategori 2 Baharu | SST wujud, tiada deraf ke PUU, hari ini − tarikh SST ≥ 120 hari | PIC + cc Ketua Unit | E-mel + Push |
| ALR-004 | Kategori 2 Eskalasi | Kategori 2 masih wujud selepas 14 hari | PIC + Ketua Unit + Pengarah | E-mel |

#### 6.2.2 Alert Berkaitan Bon Pelaksanaan

| ID | Trigger | Syarat | Penerima | Saluran |
|---|---|---|---|---|
| ALR-010 | Bon Akan Tamat (180 hari) | Tarikh tamat bon − hari ini = 180 hari | PIC | E-mel + Push |
| ALR-011 | Bon Akan Tamat (90 hari) | = 90 hari | PIC + Ketua Unit | E-mel + Push |
| ALR-012 | Bon Akan Tamat (30 hari) | = 30 hari | PIC + Ketua Unit | E-mel + Push |
| ALR-013 | Bon Akan Tamat (7 hari) | = 7 hari | PIC + Ketua Unit + Pengarah | E-mel + Push |
| ALR-014 | Bon Tarikh Tidak Sepadan | Bon dicipta/dikemaskini, tarikh tamat bon < tarikh tamat kontrak | PIC (segera) | E-mel + Push |
| ALR-015 | Bon Belum Serah (30 hari) | Kontrak status = tamat, bon status ≠ serah_balik, hari ≥ 30 | PIC + Ketua Unit | E-mel |
| ALR-016 | Bon Belum Serah Eskalasi (60 hari) | Sama seperti ALR-015 tetapi ≥ 60 hari | PIC + Pengarah Bahagian | E-mel |
| ALR-017 | Bon Belum Serah Kritikal (90 hari) | Sama seperti ALR-015 tetapi ≥ 90 hari | Setiausaha Kerajaan + Audit | E-mel |
| ALR-018 | Kontrak > RM200k Tiada Bon | Nilai kontrak > 200,000 DAN tiada rekod bon | PIC (segera) | E-mel + Push (blok status aktif sempurna) |

#### 6.2.3 Alert Berkaitan Dokumen Kontrak

| ID | Trigger | Syarat | Penerima | Saluran |
|---|---|---|---|---|
| ALR-020 | Stamping Tertangguh | Tarikh tandatangan ada, tarikh stamping tiada, hari ≥ 30 | PIC + Ketua Unit | E-mel + Push |
| ALR-021 | Deraf PUU Tertangguh | Deraf dihantar ke PUU, tiada respons selepas 14 hari (PIC perlu follow-up dengan PUU di luar sistem) | PIC + Ketua Unit | E-mel + Push |
| ALR-022 | Lanjutan Akan Tiba | Tarikh Lanjutan 1 atau 2 dalam tempoh 30 hari | PIC + Ketua Unit | E-mel + Push |

#### 6.2.4 Alert Berkaitan Penilaian Prestasi

| ID | Trigger | Syarat | Penerima | Saluran |
|---|---|---|---|---|
| ALR-030 | Peringatan Bulanan | 1 haribulan setiap bulan, untuk semua kontrak aktif | PIC | E-mel + Push |
| ALR-031 | Penilaian Lewat | Penilaian bulanan belum diisi selepas 14 haribulan | PIC + Ketua Unit | E-mel |
| ALR-032 | Prestasi Pembekal Rendah | Skor penilaian < 60% berturut-turut 2 bulan | Ketua Unit + Pengarah | E-mel |

### 6.3 Matriks Eskalasi

| Tahap | Tempoh Selepas Notifikasi Awal | Penerima Tambahan | Tindakan Sistem |
|---|---|---|---|
| Tahap 1 (Asal) | Hari 0 | PIC sahaja | Notifikasi standard |
| Tahap 2 | Hari 7 | + Ketua Unit | Reminder + audit trail |
| Tahap 3 | Hari 14 | + Pengarah Bahagian | Eskalasi rasmi + telefon |
| Tahap 4 | Hari 30 | + Setiausaha Kerajaan | Penanda dalam laporan audit bulanan |

### 6.4 Templat Notifikasi

Setiap jenis notifikasi mesti mempunyai templat yang boleh diuruskan oleh Pentadbir Sistem. Templat mesti menyokong placeholder berikut:

- `{nama_pic}` — Nama PIC
- `{no_sst}` — No. Rujukan SST
- `{tajuk_perjanjian}` — Tajuk Perjanjian
- `{nama_pembekal}` — Nama Pembekal
- `{tarikh_kritikal}` — Tarikh kritikal (mengikut konteks)
- `{hari_lagi}` — Bilangan hari sebelum / selepas tarikh kritikal
- `{pautan_sistem}` — Pautan terus ke rekod dalam sistem

#### 6.4.1 Contoh Templat E-mel — ALR-012 (Bon Akan Tamat 30 hari)

```text
Subjek: [AMARAN PENTING] Bon Pelaksanaan Akan Tamat dalam 30 Hari

Tuan / Puan {nama_pic},

Dimaklumkan bahawa bon pelaksanaan bagi kontrak berikut akan tamat
dalam tempoh {hari_lagi} hari:

  No. SST       : {no_sst}
  Tajuk         : {tajuk_perjanjian}
  Pembekal      : {nama_pembekal}
  Tarikh Tamat  : {tarikh_kritikal}

Tindakan yang diperlukan:
1. Semak status kontrak — adakah masih aktif atau perlu lanjutan.
2. Hubungi pembekal jika bon perlu diperbaharui.
3. Kemaskini status bon dalam sistem.

Sila klik pautan berikut: {pautan_sistem}

Pemberitahuan automatik — Sistem Pengurusan Kontrak SUK Kedah
```

### 6.5 Konfigurasi Alert oleh Pengguna

Setiap pengguna mesti boleh menetapkan keutamaan notifikasi melalui halaman 'Tetapan Notifikasi':

- Pengaktifan / penyahaktifan jenis alert tertentu
- Pemilihan saluran pilihan (e-mel sahaja, e-mel + push, dll.)
- Waktu tidak diganggu (do not disturb hours)
- Frekuensi rumusan harian / mingguan (digest mode)

---

## 7. Progressive Web Application (PWA)

Sistem akan dibangunkan sebagai **Progressive Web Application (PWA)** yang membenarkan capaian melalui peranti mudah alih (iOS dan Android) tanpa perlu pembangunan aplikasi native berasingan.

### 7.1 Keperluan Umum PWA

| ID | Keperluan | Keutamaan |
|---|---|---|
| PWA-001 | Sistem mesti menyediakan Web App Manifest (`manifest.json`) dengan ikon dan tetapan paparan yang lengkap. | Wajib |
| PWA-002 | Sistem mesti menyokong Service Worker untuk caching aset statik dan mod offline asas. | Wajib |
| PWA-003 | Sistem mesti berjalan ke atas HTTPS dengan sijil SSL sah (TLS 1.2+). | Wajib |
| PWA-004 | Sistem mesti melepasi audit Lighthouse PWA dengan skor ≥ 90. | Wajib |
| PWA-005 | PWA mesti memaparkan splash screen kustom dengan logo SUK Kedah semasa pemuatan. | Wajib |
| PWA-006 | Saiz bundle JavaScript awal mesti < 500KB (gzipped) untuk pemuatan pantas pada 3G. | Wajib |
| PWA-007 | PWA mesti menyokong dark mode mengikut tetapan sistem peranti. | Boleh Tambah |

### 7.2 Sokongan iOS (Safari)

| ID | Keperluan | Keutamaan |
|---|---|---|
| PWA-IOS-001 | PWA mesti berfungsi pada iOS 16.4 ke atas (versi yang menyokong Web Push API). | Wajib |
| PWA-IOS-002 | PWA mesti menyediakan tutorial 'Tambah ke Skrin Utama' pada lawatan pertama. | Wajib |
| PWA-IOS-003 | Push notification mesti berfungsi pada iOS selepas PWA dipasang ke skrin utama, menggunakan APNS melalui FCM. | Wajib |
| PWA-IOS-004 | Ikon dan splash screen mesti disediakan dalam pelbagai saiz (180x180, 152x152, dll.). | Wajib |
| PWA-IOS-005 | Status bar dan safe area pada iPhone mesti dikendalikan dengan betul. | Wajib |
| PWA-IOS-006 | PWA mesti boleh diakses dalam standalone mode selepas dipasang. | Wajib |

### 7.3 Sokongan Android (Chrome / Edge)

| ID | Keperluan | Keutamaan |
|---|---|---|
| PWA-AND-001 | PWA mesti berfungsi pada Android 9.0 (Pie) ke atas. | Wajib |
| PWA-AND-002 | PWA mesti menyokong install prompt automatik (`beforeinstallprompt` event). | Wajib |
| PWA-AND-003 | Push notification mesti berfungsi menggunakan FCM dengan Web Push protocol. | Wajib |
| PWA-AND-004 | PWA mesti boleh dipasang melalui Play Store dengan TWA bagi fasa 2. | Boleh Tambah |
| PWA-AND-005 | Tema warna PWA mesti diintegrasikan dengan toolbar Android. | Wajib |
| PWA-AND-006 | Maklumbalas haptik (vibration) mesti disokong untuk notifikasi penting. | Boleh Tambah |

### 7.4 Push Notification Architecture

```
┌─────────────────────────────────────────────────────────────┐
│  ALIRAN PUSH NOTIFICATION                                   │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. Pengguna log masuk PWA pada peranti mudah alih           │
│         ↓                                                    │
│  2. PWA minta kebenaran Notifications API                    │
│         ↓                                                    │
│  3. Service Worker daftar dengan FCM, dapat token            │
│         ↓                                                    │
│  4. Token disimpan dalam jadual push_subscriptions           │
│         ↓                                                    │
│  5. Scheduler Laravel jalankan peraturan alert (8:00 AM)     │
│         ↓                                                    │
│  6. Job push dimasukkan ke Redis Queue                       │
│         ↓                                                    │
│  7. Queue Worker hantar ke FCM API                           │
│         ↓                                                    │
│  8. FCM hantar ke peranti (iOS via APNS / Android via FCM)   │
│         ↓                                                    │
│  9. Service Worker terima dan paparkan notifikasi            │
│         ↓                                                    │
│ 10. Pengguna klik notifikasi, PWA dibuka ke skrin berkaitan  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 7.5 Mod Offline (Offline-First Strategy)

| Fungsi | Capaian Offline | Strategi Cache |
|---|---|---|
| Paparan dashboard (data terakhir) | Ya, data cache | Stale-While-Revalidate |
| Senarai SST aktif | Ya, baca sahaja | Cache-First dengan refresh |
| Daftar SST baharu | Tidak (perlu validasi server) | — |
| Lihat butiran kontrak | Ya jika sudah pernah dilawati | Cache-First |
| Penilaian prestasi | Boleh draf, sync apabila online | Background Sync API |
| Notifikasi in-app | Cache 50 notifikasi terakhir | Cache-First |
| Aset statik (CSS, JS, fonts) | Cache semua | Cache-First |

### 7.6 Saiz & Prestasi

- **First Contentful Paint (FCP):** < 1.8 saat
- **Largest Contentful Paint (LCP):** < 2.5 saat
- **Time to Interactive (TTI):** < 3.8 saat
- **Cumulative Layout Shift (CLS):** < 0.1
- **Total bundle size:** < 500KB (gzipped) untuk halaman utama

---

## 8. Keperluan Bukan Fungsian

### 8.1 Prestasi (Performance)

| ID | Keperluan | Metrik |
|---|---|---|
| NFR-P-001 | Masa muat halaman utama (dashboard) | ≤ 3 saat pada bandwidth 4G |
| NFR-P-002 | Masa respons API (95 percentile) | ≤ 500 ms |
| NFR-P-003 | Sokongan pengguna serentak (concurrent) | ≥ 200 pengguna tanpa degradasi |
| NFR-P-004 | Masa pemprosesan laporan kompleks | ≤ 10 saat untuk 1000 rekod |
| NFR-P-005 | Throughput penghantaran notifikasi | ≥ 100 mesej per minit |
| NFR-P-006 | Penjanaan PDF laporan | ≤ 5 saat per dokumen |

### 8.2 Keselamatan (Security)

| ID | Keperluan | Implementasi |
|---|---|---|
| NFR-S-001 | Penyulitan data semasa transit | TLS 1.3 untuk semua komunikasi |
| NFR-S-002 | Penyulitan data semasa simpanan (at rest) | AES-256 untuk maklumat sensitif |
| NFR-S-003 | Penyulitan kata laluan | Bcrypt dengan minimum 12 rounds |
| NFR-S-004 | Perlindungan SQL Injection | Eloquent ORM + Prepared Statements |
| NFR-S-005 | Perlindungan XSS | Blade auto-escape + CSP headers |
| NFR-S-006 | Perlindungan CSRF | Laravel CSRF token untuk semua POST/PUT/DELETE |
| NFR-S-007 | Rate limiting | 60 request/minit per pengguna untuk endpoint umum |
| NFR-S-008 | Polisi keselamatan kata laluan | Min. 8 aksara, kompleks, expire 90 hari, history 5 |
| NFR-S-009 | Two-Factor Authentication | TOTP untuk semua peranan kecuali umum |
| NFR-S-010 | Audit logging | Semua tindakan CRUD dilog dengan IP, user agent, timestamp |
| NFR-S-011 | Backup pangkalan data | Penuh harian, incremental setiap 6 jam |
| NFR-S-012 | Pematuhan Akta 709 | Persetujuan jelas, hak akses data, hak padam |

### 8.3 Ketersediaan (Availability)

- Target uptime: **99.5%** (≈ 3.65 jam downtime maksimum sebulan), tidak termasuk penyelenggaraan terancang.
- Penyelenggaraan terancang: maksimum 4 jam sebulan, dijalankan di luar waktu pejabat.
- Pemulihan bencana (Disaster Recovery): **RTO ≤ 4 jam, RPO ≤ 1 jam**.
- Sistem mesti menyokong rolling deployment untuk update tanpa downtime.

### 8.4 Skalabiliti

- Senibina mesti menyokong skala mendatar (horizontal scaling) — boleh tambah app server jika perlu.
- Pangkalan data mesti menyokong read replica untuk laporan dan analitik.
- Queue worker boleh ditingkat bilangan secara dinamik berdasarkan beban.
- Reka bentuk pangkalan data mesti menyokong sehingga **100,000 rekod kontrak** tanpa degradasi prestasi.

### 8.5 Kebolehgunaan (Usability)

- Antara muka mesti mematuhi **WCAG 2.1 Level AA** untuk kebolehcapaian.
- Bahasa lalai: Bahasa Malaysia, dengan pilihan tukar ke Bahasa Inggeris.
- Reka bentuk responsif untuk skrin desktop, tablet dan mobile.
- Maklum balas visual untuk semua tindakan (loading, success, error).
- Latihan dalam aplikasi (in-app tour) untuk pengguna baharu.

### 8.6 Kebolehselenggaraan

- Kod mesti ikut **PSR-12** untuk PHP dan **ESLint Airbnb** untuk JavaScript.
- Liputan ujian unit minimum **70%** untuk modul kritikal (Auth, RBAC, Alert).
- Dokumentasi API menggunakan **OpenAPI 3.0**.
- Logging berstruktur (structured logging) menggunakan Monolog dengan format JSON.

---

## 9. Senibina Teknikal & Stack

### 9.1 Stack Teknologi Cadangan

| Komponen | Teknologi | Versi | Alasan Pemilihan |
|---|---|---|---|
| Bahasa Pengaturcaraan | PHP | 8.2 LTS | Stabil, sokongan panjang |
| Framework Backend | Laravel | 11.x LTS | Standard kerajaan Malaysia, ekosistem matang |
| Admin Panel Framework | FilamentPHP | 3.x | Modern admin panel, CRUD generator, form/table builder, dashboard widgets, TALL stack native |
| Pangkalan Data | MySQL | 8.0 | Diperlukan oleh spec, stabil |
| Cache & Queue | Redis | 7.x | Cepat, sokongan baik dalam Laravel |
| Web Server | Nginx | 1.24+ | Prestasi tinggi |
| Frontend Stack (TALL) | Tailwind CSS + Alpine.js + Livewire + Laravel | 3.x / 3.x / 3.x / 11.x | TALL stack - fully integrated, reactive tanpa JavaScript framework kompleks |
| Build Tool | Vite | 5.x | Pantas, integrasi Laravel |
| PWA Library | Workbox | 7.x | Generate Service Worker, oleh Google |
| Push Notification | Firebase Cloud Messaging | — | Sokong iOS + Android |
| Authentication | Laravel Sanctum + Filament Auth | 4.x / 3.x | API token & built-in Filament authentication |
| RBAC | spatie/laravel-permission + Filament Shield | 6.x / 3.x | Standard RBAC + Filament UI integration |
| Audit Trail | owen-it/laravel-auditing | 13.x | Track semua perubahan model |
| Excel Import/Export | maatwebsite/excel + Filament Excel | 3.x / 2.x | Industry standard + Filament actions |
| PDF Generation | barryvdh/laravel-dompdf | 3.x | Untuk laporan PDF |
| Chart Rendering | Filament Widgets + ApexCharts | 3.x | Built-in chart widgets untuk dashboard |
| Hosting | On-Premise / Sovereign Cloud | — | Polisi Kerajaan Negeri |
| OS Server | Ubuntu LTS | 22.04 / 24.04 | Stabil, sokongan panjang |

### 9.2 Package Laravel Tambahan

```text
# Filament Core & Plugins
filament/filament              # Core admin panel framework v3.x
bezhansalleh/filament-shield   # RBAC UI for spatie/permission
filament/spatie-laravel-media-library-plugin  # Media library integration
pxlrbt/filament-excel          # Excel export actions
filament/notifications         # In-app notifications UI

# Laravel Core Packages
spatie/laravel-permission      # RBAC framework
owen-it/laravel-auditing       # Audit trail untuk model
laravel/sanctum                # API authentication
laravel/horizon                # Queue dashboard & monitoring
laravel/telescope              # Debug tool (development sahaja)

# External Integrations
kreait/laravel-firebase        # Firebase Cloud Messaging integration
maatwebsite/excel              # Excel import/export
barryvdh/laravel-dompdf        # PDF generation

# Utilities
intervention/image             # Image manipulation
spatie/laravel-activitylog     # Activity logging (alternatif)
spatie/laravel-medialibrary    # File attachments management
spatie/laravel-backup          # Automated backup
predis/predis                  # Redis client
guzzlehttp/guzzle              # HTTP client untuk API integrasi
```

### 9.3 Senibina Deployment

```
  ┌───────────────────────────────────────────────────┐
  │  PERSEKITARAN PRODUCTION                          │
  ├───────────────────────────────────────────────────┤
  │                                                   │
  │  [Load Balancer · Nginx]                          │
  │          │                                        │
  │     ┌────┴────┐                                   │
  │     ↓         ↓                                   │
  │  [App-1]   [App-2]    ← Laravel + PHP-FPM         │
  │     │         │                                   │
  │     └────┬────┘                                   │
  │          │                                        │
  │  ┌───────┼──────┬──────────────┐                  │
  │  ↓       ↓      ↓              ↓                  │
  │ [MySQL] [Redis] [S3 Storage]  [Queue Workers]    │
  │ Master  Cache   Lampiran      x3 Workers         │
  │   │     Queue                                     │
  │   ↓                                               │
  │ [Replica]  ← Untuk laporan & analitik             │
  │                                                   │
  └───────────────────────────────────────────────────┘
```

### 9.4 Spesifikasi Hardware Cadangan

#### 9.4.1 Persekitaran Production

| Komponen | Spesifikasi | Catatan |
|---|---|---|
| Application Server (x2) | 8 vCPU, 16 GB RAM, 100 GB SSD | Load balanced |
| Database Server (Master) | 16 vCPU, 32 GB RAM, 500 GB SSD | MySQL 8.0 |
| Database Server (Replica) | 8 vCPU, 16 GB RAM, 500 GB SSD | Read-only replica |
| Redis Server | 4 vCPU, 8 GB RAM, 50 GB SSD | Cache + Queue |
| Storage | S3-compatible, 1 TB initial | Lampiran dokumen |
| Bandwidth | 100 Mbps dedicated | — |

#### 9.4.2 Persekitaran Staging / UAT

- 1 unit gabungan: 8 vCPU, 16 GB RAM, 200 GB SSD
- Konfigurasi serupa production tetapi single-node

### 9.5 Keselamatan Infrastruktur

- Firewall: Hanya port 443 (HTTPS) terbuka dari internet awam
- Pelayan aplikasi & database dalam private subnet, tidak boleh diakses dari luar
- VPN diperlukan untuk akses pentadbiran
- Web Application Firewall (WAF) — boleh Cloudflare atau setara
- Intrusion Detection System (IDS) — fail2ban minimum
- Penggalian log keselamatan ke SIEM (jika ada)

---

## 10. Model Data (Database Schema)

### 10.1 Gambaran Keseluruhan

Pangkalan data MySQL terdiri daripada anggaran **28 jadual utama**, terbahagi kepada beberapa kategori:

- **Kategori 1:** Authentication & RBAC — 6 jadual
- **Kategori 2:** Master Data — 7 jadual
- **Kategori 3:** Core Transaction — 8 jadual
- **Kategori 4:** Notification & Alert — 4 jadual
- **Kategori 5:** Audit & Logging — 3 jadual

### 10.2 Jadual Utama

#### 10.2.1 Kategori: Authentication & RBAC

| Nama Jadual | Penerangan | Kunci Utama |
|---|---|---|
| `users` | Pengguna sistem (semua peranan) | `id` (BIGINT, AI) |
| `roles` | Peranan terbina dalam dan tersuai | `id` (BIGINT, AI) |
| `permissions` | Kebenaran granular (modul.tindakan) | `id` (BIGINT, AI) |
| `model_has_roles` | Pivot: pengguna ↔ peranan (Spatie) | (role_id, model_id, model_type) |
| `model_has_permissions` | Pivot: pengguna ↔ kebenaran langsung | (permission_id, model_id, model_type) |
| `role_has_permissions` | Pivot: peranan ↔ kebenaran (Spatie) | (role_id, permission_id) |

#### 10.2.2 Kategori: Master Data

| Nama Jadual | Penerangan | Kunci Utama |
|---|---|---|
| `jabatan` | Senarai jabatan SUK Kedah | `kod_jabatan` (VARCHAR) |
| `seksyen_unit` | Seksyen / unit dalam jabatan | `id` |
| `pembekal` | Cache pembekal dari iDaftar | `no_pendaftaran` |
| `kaedah_perolehan` | Jenis kaedah (tender, sebut harga, dll.) | `id` |
| `kategori_skop` | Bekalan / Perkhidmatan / Kerja | `id` |
| `status_kontrak` | Senarai status (aktif, tamat, dll.) | `id` |
| `bank_pengeluar_bon` | Senarai bank pengeluar bon | `id` |

#### 10.2.3 Kategori: Core Transaction

| Nama Jadual | Penerangan | Kunci Utama |
|---|---|---|
| `daftar_sst` | Surat Setuju Terima — jadual utama | `id` |
| `daftar_kontrak` | Penjejakan dokumen kontrak formal | `id` |
| `bon_pelaksanaan` | Bon / jaminan bank | `id` |
| `insurans_kontrak` | Insurans (alternatif kepada bon) | `id` |
| `lanjutan_tempoh` | Rekod lanjutan kontrak | `id` |
| `penilaian_prestasi` | Penilaian pembekal bulanan | `id` |
| `lampiran_dokumen` | Fail PDF, gambar yang dimuat naik | `id` |
| `status_kontrak_log` | Log perubahan status (tracking flow) | `id` |

#### 10.2.4 Kategori: Notification & Alert

| Nama Jadual | Penerangan | Kunci Utama |
|---|---|---|
| `notifications` | In-app notifications (Laravel default) | `id` (UUID) |
| `alert_rules` | Konfigurasi peraturan alert | `id` |
| `alert_history` | Log notifikasi yang telah dihantar | `id` |
| `push_subscriptions` | Token FCM untuk push notification | `id` |

#### 10.2.5 Kategori: Audit & Logging

| Nama Jadual | Penerangan | Kunci Utama |
|---|---|---|
| `audits` | Perubahan rekod model (owen-it/auditing) | `id` |
| `activity_log` | Log aktiviti umum (spatie/activitylog) | `id` |
| `login_history` | Log log masuk berjaya/gagal | `id` |

### 10.3 Hubungan Antara Jadual (ERD Ringkas)

```
users ──┬── roles (via model_has_roles)
        ├── permissions (via model_has_permissions)
        ├── jabatan (BELONGS TO)
        ├── seksyen_unit (BELONGS TO)
        └── push_subscriptions (HAS MANY)

daftar_sst ──┬── pembekal (BELONGS TO)
             ├── users [pic_id] (BELONGS TO)
             ├── seksyen_unit (BELONGS TO)
             ├── jabatan (BELONGS TO)
             ├── kaedah_perolehan (BELONGS TO)
             ├── daftar_kontrak (HAS ONE)
             ├── bon_pelaksanaan (HAS ONE)
             ├── insurans_kontrak (HAS ONE)
             ├── lanjutan_tempoh (HAS MANY)
             ├── penilaian_prestasi (HAS MANY)
             ├── lampiran_dokumen (HAS MANY)
             └── status_kontrak_log (HAS MANY)

alert_rules ──── alert_history (HAS MANY)
alert_history ── users (BELONGS TO recipient)
```

### 10.4 Sampel Skema: Jadual `daftar_sst`

```sql
CREATE TABLE daftar_sst (
  id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  no_rujukan_sst        VARCHAR(50)  NOT NULL UNIQUE,
  tarikh_sst            DATE         NOT NULL,
  jabatan_kod           VARCHAR(10)  NOT NULL,
  seksyen_unit_id       BIGINT UNSIGNED NOT NULL,
  pic_id                BIGINT UNSIGNED NOT NULL,
  pembekal_no_daftar    VARCHAR(50)  NOT NULL,
  pembekal_pic_nama     VARCHAR(200),
  pembekal_pic_telefon  VARCHAR(20),
  pembekal_pic_emel     VARCHAR(120),
  skop                  ENUM('bekalan','perkhidmatan','kerja') NOT NULL,
  kaedah_perolehan_id   BIGINT UNSIGNED NOT NULL,
  tajuk_perjanjian      VARCHAR(500) NOT NULL,
  no_perolehan          VARCHAR(50),
  no_lo                 VARCHAR(50),
  tarikh_lo             DATE,
  nilai_kontrak         DECIMAL(15,2) NOT NULL,
  tempoh_kontrak_bulan  TINYINT UNSIGNED,
  tarikh_mula           DATE,
  tarikh_tamat          DATE,
  tarikh_lanjutan_1     DATE NULL,
  tarikh_lanjutan_2     DATE NULL,
  kontrak_formal        BOOLEAN DEFAULT TRUE,
  kategori_risiko       ENUM('normal','kategori_1','kategori_2') DEFAULT 'normal',
  status                ENUM('aktif','tamat','dibatalkan') DEFAULT 'aktif',
  penalti_klausa        TEXT,
  created_by            BIGINT UNSIGNED NOT NULL,
  updated_by            BIGINT UNSIGNED,
  created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at            TIMESTAMP NULL,  -- soft delete

  INDEX idx_jabatan (jabatan_kod),
  INDEX idx_pic (pic_id),
  INDEX idx_status (status, kategori_risiko),
  INDEX idx_tarikh (tarikh_sst, tarikh_tamat),
  FOREIGN KEY (jabatan_kod) REFERENCES jabatan(kod_jabatan),
  FOREIGN KEY (pic_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 11. Integrasi Sistem

Sistem ini perlu berintegrasi dengan beberapa sistem sedia ada untuk mengelakkan kemasukan data berulang dan memastikan ketepatan maklumat. Semua integrasi adalah dengan **API HTTPS dengan pengesahan token**.

### 11.1 Integrasi dengan API EPSM (Pegawai Awam)

- **Endpoint:** `https://epsm.kedah.gov.my/api_kuarters.php?secret_key={KEY}&no_kp={IC}`
- **Tujuan:** Memuatkan butiran pegawai semasa pendaftaran pengguna baharu.
- **Method:** GET
- **Data yang diambil:** Nama, Jabatan, Jawatan, E-mel rasmi, No. Telefon.
- **Frekuensi:** Pada pendaftaran sahaja dan apabila pengguna meminta refresh profile.

### 11.2 Integrasi dengan API iDaftar (Pembekal)

- **Tujuan:** Memuatkan butiran pembekal berdasarkan No. Pendaftaran.
- **Data yang diambil:** Nama syarikat, status pendaftaran, kategori, alamat, status MOF.
- **Caching:** Data pembekal di-cache dalam jadual `pembekal` dengan TTL 7 hari.
- **Fallback:** Jika API tidak responsif, gunakan data cache yang sedia ada.

### 11.3 Integrasi dengan API ePerolehan / ATS

- **Tujuan:** Memuatkan butiran perolehan seperti No. Perolehan, Tajuk Perjanjian, Nilai Kontrak.
- **Data yang diambil:** Maklumat dari sistem perolehan kerajaan negeri.
- **Method:** GET dengan filter mengikut No. Perolehan.

### 11.4 Integrasi dengan SMTP Server

- Untuk penghantaran e-mel notifikasi.
- Pilihan: Server SMTP Kerajaan Negeri atau perkhidmatan pihak ketiga (SendGrid / Mailgun).
- Konfigurasi melalui Laravel Mail driver.

### 11.5 Integrasi dengan Firebase Cloud Messaging (FCM)

- Untuk push notification PWA ke iOS dan Android.
- Memerlukan projek Firebase dan service account key.
- Implementasi melalui package `kreait/laravel-firebase`.
- Push notification berfungsi sebagai pengganti SMS untuk amaran kritikal — muncul pada peranti dengan bunyi dan getaran.

### 11.6 Pengurusan Kegagalan Integrasi

- **Retry policy:** 3 kali percubaan dengan backoff exponential (1s, 3s, 9s).
- **Circuit breaker:** Selepas 10 kegagalan berturut-turut, suspend panggilan ke API tersebut selama 5 minit.
- **Logging:** Semua kegagalan dilog dengan butiran request/response.
- **Notifikasi:** Pentadbir Sistem dimaklumkan jika satu integrasi gagal melebihi 30 minit.

---

## 12. Reka Bentuk Antaramuka Pengguna

### 12.1 Prinsip Reka Bentuk

- **Kejelasan:** Maklumat penting mesti mudah dilihat tanpa perlu mencari.
- **Konsistensi:** Komponen yang sama mesti berfungsi sama di seluruh sistem.
- **Keberkesanan:** Pengguna boleh menyelesaikan tugas dalam bilangan klik minimum.
- **Maklum balas:** Setiap tindakan mesti diberi maklum balas visual segera.
- **Toleransi ralat:** Pengguna boleh undo tindakan dalam tempoh tertentu.
- **Pendekatan mobile-first:** Reka bentuk diutamakan untuk skrin kecil dahulu.
- **FilamentPHP Design System:** Menggunakan Filament v3 design system dengan komponen yang konsisten, accessible (WCAG 2.1), dan responsive.

### 12.2 Halaman Utama Sistem

| Halaman | Tujuan | Pengguna Utama |
|---|---|---|
| Login | Log masuk dengan e-mel + kata laluan + 2FA | Semua |
| Dashboard Eksekutif | Paparan KPI, Gantt, kalendar risiko, funnel | Exec, Pengarah |
| Dashboard PIC | Senarai tugasan, alert, tindakan diperlukan | PIC, Ketua Unit |
| Daftar SST | Borang pendaftaran SST dengan auto-fill API | PIC |
| Senarai SST | Senarai dengan carian, tapis, eksport | Semua kecuali audit |
| Butiran Kontrak | Pandangan penuh satu kontrak dengan tab | Semua |
| Daftar Bon | Borang pendaftaran bon pelaksanaan | PIC |
| Penilaian Prestasi | Borang penilaian bulanan | PIC, Ketua Unit |
| Laporan | Senarai laporan, parameter, eksport | Pengarah, Audit |
| Tetapan Pengguna | Profil, kata laluan, notifikasi | Semua |
| Pengurusan Pengguna | CRUD pengguna dan peranan | Admin |
| Master Data | Pengurusan jabatan, seksyen, kod | Admin |
| Konfigurasi Alert | Edit peraturan dan templat alert | Admin |

### 12.3 Komponen UI Standard (Filament Built-in)

- **Navigation:** Sidebar navigasi dengan grouping mengikut modul, responsive collapse untuk mobile.
- **Global Search:** Carian global untuk SST, kontrak, pembekal (Filament global search).
- **User Menu:** Avatar pengguna dengan dropdown (profil, tetapan, log keluar).
- **Notifications:** Notification panel dengan badge count untuk unread alerts.
- **Tables:** Filament Table Builder dengan filters, search, bulk actions, export, pagination.
- **Forms:** Filament Form Builder dengan validation, wizard steps untuk borang kompleks.
- **Actions:** Action buttons dengan confirmation modals, success/error notifications.
- **Widgets:** Dashboard widgets untuk KPI cards, charts (Gantt, funnel, heatmap).
- **Modal & Slide-over:** Untuk CRUD operations dan quick views.
- **Notifications (Toast):** Maklum balas visual untuk semua tindakan.

### 12.4 Reka Bentuk Visual

- **Warna utama:** Navy `#0B1A2B` — merujuk warna rasmi institusi (customize Filament primary color).
- **Warna aksen:** Emas `#B8893A` — untuk penonjolan kerajaan negeri (customize Filament secondary color).
- **Warna semantik:** Merah untuk kritikal, kuning untuk amaran, hijau untuk OK, biru untuk maklumat.
- **Typography:** Inter font (Filament default) untuk badan teks, dengan pilihan font Bahasa Malaysia yang sesuai.
- **Spacing:** Sistem grid 8-pixel untuk konsistensi (Tailwind default).
- **Iconography:** Heroicons (Filament default) — set ikon konsisten dan accessible.
- **Dark Mode:** Sokongan dark mode Filament (opsional untuk pengguna).

---

## 13. Pelaksanaan & Migrasi

### 13.1 Fasa Pelaksanaan

| Fasa | Skop | Tempoh | Penghantaran |
|---|---|---|---|
| Fasa 0: Inisiasi | Kick-off, finalisasi PRD, persetujuan SLA | 2 minggu | Project Charter, signed PRD |
| Fasa 1: Senibina & Reka Bentuk | Database design, API design, UI mockup, prototype | 4 minggu | DDD, UI/UX prototype, design system |
| Fasa 2: Pembangunan Sprint 1 | Modul M1 (Auth, RBAC), M9 (Master Data) | 4 minggu | Demo Sprint 1 |
| Fasa 3: Pembangunan Sprint 2 | Modul M2 (Daftar SST), M3 (Daftar Kontrak) | 4 minggu | Demo Sprint 2 |
| Fasa 4: Pembangunan Sprint 3 | Modul M4 (Bon), M5 (Penilaian Prestasi) | 4 minggu | Demo Sprint 3 |
| Fasa 5: Pembangunan Sprint 4 | Modul M6 (Dashboard), M7 (Alert Engine), M8 (Audit) | 5 minggu | Demo Sprint 4 |
| Fasa 6: PWA & Mobile | Service Worker, Push Notification, optimasi mobile | 3 minggu | PWA installable & functional |
| Fasa 7: Integrasi | API iDaftar, ePerolehan, ATS, EPSM, FCM, SMTP | 3 minggu | Integrasi penuh dengan ujian |
| Fasa 8: UAT & Pembetulan | Ujian penerimaan pengguna, pembetulan isu | 4 minggu | UAT sign-off |
| Fasa 9: Migrasi Data & Latihan | Import data sedia ada, latihan pengguna | 3 minggu | Data migrated, users trained |
| Fasa 10: Go-Live & Sokongan | Pelancaran rasmi, sokongan hypercare | 2 minggu hypercare + 12 bulan warranty | Sistem aktif di production |

> **Jumlah tempoh anggaran: 38 minggu (≈ 9 bulan)** dari kick-off hingga go-live.

### 13.2 Pendekatan Pembangunan

- **Metodologi:** Agile Scrum dengan sprint 2 minggu.
- **Ceremonies:** Daily standup, sprint planning, sprint review, retrospective.
- **Tools:** GitLab / GitHub untuk version control, Jira / Trello untuk tracking.
- **Persekitaran:** Development → Staging → Production dengan CI/CD automatik.

### 13.3 Migrasi Data

Data sedia ada dalam helaian Excel akan dimigrasikan ke sistem baharu mengikut pendekatan berikut:

1. **Pengumpulan & pembersihan:** Audit dan pembersihan data Excel sedia ada (anggaran 170 rekod kontrak).
2. **Pemetaan field:** Padankan kolum Excel kepada skema baharu dengan ujian transformasi.
3. **Migrasi pukal:** Import melalui skrip Laravel `artisan` dengan validasi setiap rekod.
4. **Verifikasi:** PIC mengesahkan ketepatan data masing-masing dalam tempoh 2 minggu.
5. **Cutover:** Tarikh rasmi pemberhentian helaian Excel sebagai sumber kebenaran.

### 13.4 Latihan Pengguna

| Kumpulan | Bilangan Sesi | Tempoh | Format |
|---|---|---|---|
| Pengurusan (Exec, Pengarah) | 2 sesi | Setengah hari | Bertatap muka + dokumentasi |
| Ketua Unit | 3 sesi | Sehari | Bertatap muka + hands-on |
| PIC | 8 sesi | Sehari penuh | Bertatap muka + hands-on lab |
| Pegawai Audit | 1 sesi | Setengah hari | Bertatap muka |
| Pentadbir Sistem | 1 sesi | 3 hari | Latihan teknikal mendalam |
| Bahan Latihan | — | — | Video tutorial, manual PDF, FAQ |

### 13.5 Kriteria Penerimaan (Acceptance Criteria)

Sistem dikira siap untuk go-live apabila semua kriteria berikut dipenuhi:

1. Semua keperluan fungsian (FR) dengan keutamaan 'Wajib' telah dilaksanakan dan diuji.
2. Semua keperluan bukan fungsian (NFR) dipenuhi mengikut metrik.
3. UAT lulus dengan kelulusan rasmi dari setiap kumpulan pengguna.
4. Ujian keselamatan (penetration testing) tidak menjumpai isu Critical atau High yang belum diperbaiki.
5. Liputan ujian unit ≥ 70% untuk modul kritikal.
6. Audit Lighthouse PWA ≥ 90.
7. Latihan pengguna telah dijalankan untuk semua kumpulan.
8. Migrasi data lengkap dengan kelulusan PIC.
9. Dokumentasi pengguna, dokumentasi teknikal dan API documentation lengkap.
10. Pasukan sokongan teknikal sedia untuk fasa hypercare.

### 13.6 Penyelenggaraan Pasca Go-Live

- **Fasa Hypercare:** 2 minggu sokongan intensif on-site selepas go-live.
- **Warranty:** 12 bulan pembetulan bug tanpa kos tambahan.
- **SLA:** Sokongan tahap 1 (24 jam), tahap 2 (8 jam), tahap 3 (2 jam) untuk isu kritikal.
- **Penyelenggaraan berjadual:** Patch keselamatan bulanan, upgrade tahunan.

---

*Tamat dokumen — PRD Sistem Pengurusan Kontrak SUK Kedah · v1.0 · 12 Mei 2026*
