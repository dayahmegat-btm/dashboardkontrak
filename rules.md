# Business Rules & System Logic
## Sistem Pengurusan Dokumen Kontrak & Bon Pelaksanaan

**Versi:** 1.0
**Tarikh:** 12 Mei 2026

---

## Daftar Kandungan

1. [Kontrak Kategori & Status](#kontrak-kategori--status)
2. [Bon Pelaksanaan Rules](#bon-pelaksanaan-rules)
3. [Alert Rules Engine (18 Rules)](#alert-rules-engine-18-rules)
4. [Validation Rules](#validation-rules)
5. [Workflow & Approval Rules](#workflow--approval-rules)
6. [RBAC & Permission Rules](#rbac--permission-rules)
7. [Data Scoping Rules](#data-scoping-rules)
8. [Calculation & Auto-computation](#calculation--auto-computation)
9. [Integration Rules](#integration-rules)
10. [Security & Compliance Rules](#security--compliance-rules)
11. [Audit Trail Rules](#audit-trail-rules)
12. [Data Retention & Archival](#data-retention--archival)

---

## Kontrak Kategori & Status

### Kategori Kontrak (Risk Categories)

#### **Kategori 1: Kontrak Tanpa Dokumen Formal**

**Definisi:**
Kontrak yang SST telah dikeluarkan tetapi deraf kontrak belum dihantar kepada PUU, sementara tarikh tamat kontrak akan tiba dalam tempoh 6 bulan.

**Syarat:**
```
IF (
    daftar_sst.tarikh_sst IS NOT NULL
    AND daftar_kontrak.tarikh_deraf_ke_puu IS NULL
    AND DATEDIFF(daftar_sst.tarikh_tamat, CURDATE()) <= 180
    AND daftar_sst.kontrak_formal = TRUE
    AND daftar_sst.status = 'aktif'
) THEN
    daftar_kontrak.is_kategori_1 = TRUE
END IF
```

**Risiko:** Kontrak tamat tanpa dokumen formal yang sah, mendedahkan kerajaan kepada risiko undang-undang.

**Tindakan Required:**
1. Hantar deraf ke PUU dalam tempoh 14 hari
2. Alert PIC + Ketua Unit segera
3. Eskalasi ke Pengarah jika tidak diselesaikan dalam 7 hari

---

#### **Kategori 2: Dokumen Tertangguh Melebihi 4 Bulan**

**Definisi:**
Kontrak yang dokumen kontraknya belum disubmit ke PUU melebihi 4 bulan dari tarikh SST dikeluarkan.

**Syarat:**
```
IF (
    daftar_sst.tarikh_sst IS NOT NULL
    AND daftar_kontrak.tarikh_deraf_ke_puu IS NULL
    AND DATEDIFF(CURDATE(), daftar_sst.tarikh_sst) >= 120
    AND daftar_sst.kontrak_formal = TRUE
    AND daftar_sst.status = 'aktif'
) THEN
    daftar_kontrak.is_kategori_2 = TRUE
END IF
```

**Risiko:** Kegagalan mematuhi keperluan pekeliling perbendaharaan.

**Tindakan Required:**
1. Hantar deraf ke PUU SEGERA
2. Alert PIC + Ketua Unit segera
3. Eskalasi ke Pengarah jika tidak diselesaikan dalam 14 hari

---

### Status Kontrak Lifecycle

**Status Flow:**
```
SST Dikeluarkan → Deraf → Ke PUU → Terima dari PUU → Tandatangan → Stamping → Aktif → Tamat/Siap
```

**Status Rules:**

| Status | Kondisi | Syarat Seterusnya |
|---|---|---|
| `aktif` | SST dikeluarkan, kontrak aktif | Dokumen dalam proses atau siap |
| `lanjutan` | Lanjutan tempoh diluluskan | Tarikh lanjutan dikemaskini |
| `siap` | Kontrak siap sempurna | `tarikh_stamping` NOT NULL |
| `tamat` | Kontrak tamat tempoh | Bon mesti diserah balik |
| `dibatalkan` | Kontrak dibatalkan rasmi | Audit trail required |

**Rule: Kontrak Formal Wajib**
```
IF tempoh_kontrak_bulan > 4 THEN
    kontrak_formal = TRUE
    -- Deraf kontrak ke PUU diperlukan
END IF
```

---

## Bon Pelaksanaan Rules

### Rule 1: Bon Wajib untuk Kontrak > RM 200,000

**Business Rule:**
```
IF daftar_sst.nilai_kontrak > 200000 THEN
    -- Bon pelaksanaan OR insurans MUST exist
    ASSERT EXISTS (
        SELECT 1 FROM bon_pelaksanaan WHERE daftar_sst_id = daftar_sst.id
        OR
        SELECT 1 FROM insurans_kontrak WHERE daftar_sst_id = daftar_sst.id
    )

    IF NOT EXISTS THEN
        TRIGGER Alert ALR-018
        BLOCK status_kontrak = 'siap'
    END IF
END IF
```

**Enforcement:**
- Validation pada form submit
- Alert ALR-018 dihantar segera kepada PIC
- Status kontrak tidak boleh ditukar ke 'siap' sehingga bon wujud

---

### Rule 2: Mutually Exclusive - Bon OR Insurans

**Business Rule:**
```
-- Setiap SST hanya boleh mempunyai SATU daripada:
-- 1. Bon pelaksanaan (bon_pelaksanaan table)
-- 2. Insurans (insurans_kontrak table)
-- TIDAK KEDUA-DUANYA

ASSERT (
    (EXISTS bon_pelaksanaan AND NOT EXISTS insurans_kontrak)
    OR
    (EXISTS insurans_kontrak AND NOT EXISTS bon_pelaksanaan)
)
```

**Enforcement:**
- Application-level validation
- Form tidak membenarkan submit kedua-dua jenis
- Filament resource validation rule

---

### Rule 3: Tarikh Bon vs Tarikh Kontrak

**Business Rule:**
```
IF bon_pelaksanaan.tarikh_tamat_bon < daftar_sst.tarikh_tamat THEN
    bon_pelaksanaan.is_tarikh_valid = FALSE
    TRIGGER Alert ALR-014 (Bon Tarikh Tidak Sepadan)

    -- Generate notis automatik kepada pembekal
    GENERATE pembekal_notice (
        "Sila perbaharui bon pelaksanaan sebelum " + tarikh_tamat_bon,
        recipient: pembekal_pic_emel
    )
END IF
```

**Tindakan:**
- Alert segera kepada PIC
- Notis email kepada pembekal
- Dashboard warning indicator

---

### Rule 4: Status Bon Lifecycle

**Status Transition:**
```
aktif → akan_tamat (≤180 hari) → tamat → serah_balik/dalam_simpanan
```

**Auto-status Update:**
```sql
-- Run daily by scheduler at 8:00 AM
UPDATE bon_pelaksanaan
SET status_bon = CASE
    WHEN DATEDIFF(tarikh_tamat_bon, CURDATE()) <= 0 THEN 'tamat'
    WHEN DATEDIFF(tarikh_tamat_bon, CURDATE()) <= 180 THEN 'akan_tamat'
    ELSE 'aktif'
END
WHERE deleted_at IS NULL;
```

---

### Rule 5: Bon Serah Balik Deadline

**Business Rule:**
```
IF (
    daftar_sst.status = 'tamat' OR 'siap'
    AND bon_pelaksanaan.status_bon != 'serah_balik'
    AND bon_pelaksanaan.status_bon != 'dalam_simpanan'
) THEN
    deadline_serah_balik = daftar_sst.tarikh_tamat + 30 DAYS

    IF CURDATE() >= deadline_serah_balik THEN
        hari_lewat = DATEDIFF(CURDATE(), deadline_serah_balik)

        CASE
            WHEN hari_lewat >= 90 THEN TRIGGER ALR-017 (Kritikal - SK level)
            WHEN hari_lewat >= 60 THEN TRIGGER ALR-016 (Eskalasi Pengarah)
            WHEN hari_lewat >= 30 THEN TRIGGER ALR-015 (Peringatan PIC)
        END CASE
    END IF
END IF
```

---

## Alert Rules Engine (18 Rules)

### Scheduler Configuration

**Execution Schedule:**
```
Time: 08:00 AM daily (MYT)
Frequency: Daily
Timezone: Asia/Kuala_Lumpur
Command: php artisan alerts:check-daily
```

**Alert Processing Flow:**
```
1. Scheduler runs at 8:00 AM
2. Check all alert rules (ALR-001 to ALR-032)
3. Query database for matching conditions
4. Generate alert_history records
5. Queue notification jobs (email + push + in-app)
6. Update alert tracking flags
7. Log execution to activity_log
```

---

### Kategori 1: Alert Kontrak Kategori

#### **ALR-001: Kategori 1 Baharu**

**Trigger:** SST wujud, tiada deraf ke PUU, kontrak tamat dalam 6 bulan

**Syarat:**
```sql
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id
FROM daftar_sst ds
LEFT JOIN daftar_kontrak dk ON ds.id = dk.daftar_sst_id
WHERE ds.tarikh_sst IS NOT NULL
  AND dk.tarikh_deraf_ke_puu IS NULL
  AND DATEDIFF(ds.tarikh_tamat, CURDATE()) <= 180
  AND DATEDIFF(ds.tarikh_tamat, CURDATE()) > 0
  AND ds.kontrak_formal = TRUE
  AND ds.status = 'aktif'
  AND ds.deleted_at IS NULL
  AND dk.is_kategori_1 = TRUE
  -- Check not already alerted today
  AND NOT EXISTS (
    SELECT 1 FROM alert_history
    WHERE rule_code = 'ALR-001'
      AND daftar_sst_id = ds.id
      AND DATE(sent_at) = CURDATE()
  );
```

**Penerima:** PIC + cc Ketua Unit

**Saluran:** E-mel + Push Notification

**Template Email:**
```
Subjek: [AMARAN] Kategori 1 - Kontrak {no_rujukan_sst} Tamat dalam {hari_lagi} Hari

Tuan/Puan {nama_pic},

Kontrak berikut telah dikenal pasti sebagai KATEGORI 1:
  No. SST       : {no_rujukan_sst}
  Tajuk         : {tajuk_perjanjian}
  Pembekal      : {nama_pembekal}
  Tarikh Tamat  : {tarikh_tamat}
  Baki Hari     : {hari_lagi} hari

TINDAKAN SEGERA DIPERLUKAN:
⚠️ Deraf kontrak belum dihantar ke PUU
⚠️ Kontrak akan tamat dalam masa {hari_lagi} hari
⚠️ Risiko: Kontrak tamat tanpa dokumen formal

Sila ambil tindakan segera:
1. Sediakan deraf kontrak
2. Hantar ke PUU dalam tempoh 14 hari
3. Kemaskini status dalam sistem: {pautan_sistem}

---
Sistem Pengurusan Kontrak SUK Kedah (Automatik)
```

---

#### **ALR-002: Kategori 1 Berulang (Eskalasi)**

**Trigger:** Kategori 1 masih wujud selepas 7 hari alert pertama

**Syarat:**
```sql
-- Same as ALR-001 PLUS:
AND EXISTS (
    SELECT 1 FROM alert_history
    WHERE rule_code = 'ALR-001'
      AND daftar_sst_id = ds.id
      AND sent_at <= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
)
AND dk.tarikh_deraf_ke_puu IS NULL
```

**Penerima:** PIC + Ketua Unit + Pengarah Bahagian

**Saluran:** E-mel

**Eskalasi:** Tahap 2

---

#### **ALR-003: Kategori 2 Baharu**

**Trigger:** SST wujud, tiada deraf ke PUU, sudah 120+ hari

**Syarat:**
```sql
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id
FROM daftar_sst ds
LEFT JOIN daftar_kontrak dk ON ds.id = dk.daftar_sst_id
WHERE ds.tarikh_sst IS NOT NULL
  AND dk.tarikh_deraf_ke_puu IS NULL
  AND DATEDIFF(CURDATE(), ds.tarikh_sst) >= 120
  AND ds.kontrak_formal = TRUE
  AND ds.status = 'aktif'
  AND ds.deleted_at IS NULL
  AND dk.is_kategori_2 = TRUE
  AND NOT EXISTS (
    SELECT 1 FROM alert_history
    WHERE rule_code = 'ALR-003'
      AND daftar_sst_id = ds.id
      AND DATE(sent_at) = CURDATE()
  );
```

**Penerima:** PIC + cc Ketua Unit

**Saluran:** E-mel + Push Notification

---

#### **ALR-004: Kategori 2 Eskalasi**

**Trigger:** Kategori 2 masih wujud selepas 14 hari alert pertama

**Penerima:** PIC + Ketua Unit + Pengarah

**Saluran:** E-mel

**Eskalasi:** Tahap 3

---

### Kategori 2: Alert Bon Pelaksanaan

#### **ALR-010: Bon Akan Tamat (180 hari)**

**Trigger:** Tarikh tamat bon dalam 180 hari

**Syarat:**
```sql
SELECT bp.id, bp.daftar_sst_id, ds.pic_id
FROM bon_pelaksanaan bp
JOIN daftar_sst ds ON bp.daftar_sst_id = ds.id
WHERE DATEDIFF(bp.tarikh_tamat_bon, CURDATE()) = 180
  AND bp.status_bon IN ('aktif', 'akan_tamat')
  AND bp.deleted_at IS NULL
  AND ds.deleted_at IS NULL
  AND bp.alert_180_sent = FALSE;
```

**Penerima:** PIC

**Saluran:** E-mel + Push

**Post-action:**
```sql
UPDATE bon_pelaksanaan
SET alert_180_sent = TRUE
WHERE id = bp.id;
```

---

#### **ALR-011: Bon Akan Tamat (90 hari)**

**Trigger:** Tarikh tamat bon dalam 90 hari

**Syarat:**
```sql
WHERE DATEDIFF(bp.tarikh_tamat_bon, CURDATE()) = 90
  AND bp.alert_90_sent = FALSE;
```

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel + Push

**Post-action:**
```sql
UPDATE bon_pelaksanaan
SET alert_90_sent = TRUE
WHERE id = bp.id;
```

---

#### **ALR-012: Bon Akan Tamat (30 hari)**

**Trigger:** Tarikh tamat bon dalam 30 hari

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel + Push

**Post-action:**
```sql
UPDATE bon_pelaksanaan
SET alert_30_sent = TRUE
WHERE id = bp.id;
```

**Template Email:**
```
Subjek: [AMARAN PENTING] Bon Pelaksanaan Akan Tamat dalam 30 Hari

Tuan/Puan {nama_pic},

Dimaklumkan bahawa bon pelaksanaan bagi kontrak berikut akan tamat
dalam tempoh {hari_lagi} hari:

  No. SST       : {no_rujukan_sst}
  Tajuk         : {tajuk_perjanjian}
  Pembekal      : {nama_pembekal}
  No. Bon       : {no_rujukan_bon}
  Tarikh Tamat  : {tarikh_tamat_bon}
  Baki Hari     : {hari_lagi} hari

Tindakan yang diperlukan:
1. Semak status kontrak — adakah masih aktif atau perlu lanjutan
2. Hubungi pembekal jika bon perlu diperbaharui
3. Kemaskini status bon dalam sistem

Sila klik pautan berikut: {pautan_sistem}

---
Pemberitahuan automatik — Sistem Pengurusan Kontrak SUK Kedah
```

---

#### **ALR-013: Bon Akan Tamat (7 hari) - KRITIKAL**

**Trigger:** Tarikh tamat bon dalam 7 hari

**Penerima:** PIC + Ketua Unit + Pengarah

**Saluran:** E-mel + Push

**Priority:** KRITIKAL

**Post-action:**
```sql
UPDATE bon_pelaksanaan
SET alert_7_sent = TRUE,
    status_bon = 'akan_tamat'
WHERE id = bp.id;
```

---

#### **ALR-014: Bon Tarikh Tidak Sepadan**

**Trigger:** Bon dicipta/dikemaskini, tarikh tamat bon < tarikh tamat kontrak

**Syarat:**
```sql
SELECT bp.id, bp.daftar_sst_id, ds.pic_id
FROM bon_pelaksanaan bp
JOIN daftar_sst ds ON bp.daftar_sst_id = ds.id
WHERE bp.tarikh_tamat_bon < ds.tarikh_tamat
  AND bp.is_tarikh_valid = FALSE
  AND bp.deleted_at IS NULL;
```

**Penerima:** PIC (segera - triggered immediately upon save)

**Saluran:** E-mel + Push + In-App

**Action:**
```
1. Alert PIC immediately
2. Generate notice to supplier email
3. Display warning on dashboard
4. Block changing status to 'siap' until resolved
```

---

#### **ALR-015: Bon Belum Serah (30 hari)**

**Trigger:** Kontrak tamat, bon belum serah balik, sudah 30 hari

**Syarat:**
```sql
SELECT bp.id, bp.daftar_sst_id, ds.pic_id
FROM bon_pelaksanaan bp
JOIN daftar_sst ds ON bp.daftar_sst_id = ds.id
WHERE ds.status IN ('tamat', 'siap')
  AND bp.status_bon NOT IN ('serah_balik', 'dalam_simpanan')
  AND DATEDIFF(CURDATE(), ds.tarikh_tamat) >= 30
  AND DATEDIFF(CURDATE(), ds.tarikh_tamat) < 60
  AND bp.deleted_at IS NULL;
```

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel

---

#### **ALR-016: Bon Belum Serah Eskalasi (60 hari)**

**Trigger:** Kontrak tamat, bon belum serah balik, sudah 60 hari

**Penerima:** PIC + Pengarah Bahagian

**Saluran:** E-mel

**Eskalasi:** Tahap 2

---

#### **ALR-017: Bon Belum Serah Kritikal (90 hari)**

**Trigger:** Kontrak tamat, bon belum serah balik, sudah 90 hari

**Penerima:** Setiausaha Kerajaan + Pegawai Audit

**Saluran:** E-mel

**Eskalasi:** Tahap 3 (KRITIKAL - Executive Level)

**Additional Actions:**
- Laporan bulanan ke SK
- Audit finding entry
- Performance review of PIC

---

#### **ALR-018: Kontrak > RM200k Tiada Bon**

**Trigger:** Nilai kontrak > RM 200,000 DAN tiada rekod bon/insurans

**Syarat:**
```sql
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id
FROM daftar_sst ds
WHERE ds.nilai_kontrak > 200000
  AND ds.status = 'aktif'
  AND NOT EXISTS (
    SELECT 1 FROM bon_pelaksanaan WHERE daftar_sst_id = ds.id
  )
  AND NOT EXISTS (
    SELECT 1 FROM insurans_kontrak WHERE daftar_sst_id = ds.id
  )
  AND ds.deleted_at IS NULL;
```

**Penerima:** PIC (segera)

**Saluran:** E-mel + Push

**Action:**
```
BLOCK: status kontrak tidak boleh ditukar ke 'siap'
REQUIRE: Bon pelaksanaan OR insurans mesti wujud
```

---

### Kategori 3: Alert Dokumen Kontrak

#### **ALR-020: Stamping Tertangguh**

**Trigger:** Kontrak ditandatangan, tiada tarikh stamping, sudah 30 hari

**Syarat:**
```sql
SELECT dk.id, dk.daftar_sst_id, ds.pic_id
FROM daftar_kontrak dk
JOIN daftar_sst ds ON dk.daftar_sst_id = ds.id
WHERE dk.tarikh_tandatangan_kontrak IS NOT NULL
  AND dk.tarikh_stamping IS NULL
  AND DATEDIFF(CURDATE(), dk.tarikh_tandatangan_kontrak) >= 30
  AND dk.deleted_at IS NULL;
```

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel + Push

---

#### **ALR-021: Deraf PUU Tertangguh**

**Trigger:** Deraf dihantar ke PUU, tiada respons selepas 14 hari

**Syarat:**
```sql
SELECT dk.id, dk.daftar_sst_id, ds.pic_id
FROM daftar_kontrak dk
JOIN daftar_sst ds ON dk.daftar_sst_id = ds.id
WHERE dk.tarikh_deraf_ke_puu IS NOT NULL
  AND dk.tarikh_terima_dari_puu IS NULL
  AND DATEDIFF(CURDATE(), dk.tarikh_deraf_ke_puu) >= 14
  AND dk.deleted_at IS NULL;
```

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel + Push

**Note:** PIC perlu follow-up dengan PUU di luar sistem (phone/email)

---

#### **ALR-022: Lanjutan Akan Tiba**

**Trigger:** Tarikh lanjutan 1 atau 2 dalam tempoh 30 hari

**Syarat:**
```sql
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id
FROM daftar_sst ds
WHERE (
    DATEDIFF(ds.tarikh_lanjutan_1, CURDATE()) BETWEEN 0 AND 30
    OR
    DATEDIFF(ds.tarikh_lanjutan_2, CURDATE()) BETWEEN 0 AND 30
  )
  AND ds.status = 'lanjutan'
  AND ds.deleted_at IS NULL;
```

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel + Push

---

### Kategori 4: Alert Penilaian Prestasi

#### **ALR-030: Peringatan Bulanan**

**Trigger:** 1 haribulan setiap bulan, untuk semua kontrak aktif

**Syarat:**
```sql
-- Run on 1st of every month
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id
FROM daftar_sst ds
WHERE ds.status = 'aktif'
  AND ds.deleted_at IS NULL
  AND DAY(CURDATE()) = 1;
```

**Penerima:** PIC

**Saluran:** E-mel + Push

**Template:**
```
Subjek: Peringatan: Penilaian Prestasi Pembekal Bulan {bulan}

Salam {nama_pic},

Ini adalah peringatan bulanan untuk mengisi penilaian prestasi pembekal
bagi kontrak yang anda uruskan.

Sila kemaskini penilaian sebelum 14 haribulan.

Akses sistem: {pautan_sistem}
```

---

#### **ALR-031: Penilaian Lewat**

**Trigger:** Penilaian bulanan belum diisi selepas 14 haribulan

**Syarat:**
```sql
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id
FROM daftar_sst ds
WHERE ds.status = 'aktif'
  AND ds.deleted_at IS NULL
  AND DAY(CURDATE()) = 14
  AND NOT EXISTS (
    SELECT 1 FROM penilaian_prestasi pp
    WHERE pp.daftar_sst_id = ds.id
      AND pp.bulan_penilaian = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
      AND pp.tahun_penilaian = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
  );
```

**Penerima:** PIC + Ketua Unit

**Saluran:** E-mel

**Eskalasi:** Tahap 1

---

#### **ALR-032: Prestasi Pembekal Rendah**

**Trigger:** Skor penilaian < 60% berturut-turut 2 bulan

**Syarat:**
```sql
SELECT ds.id, ds.no_rujukan_sst, ds.pic_id, pp1.skor_purata, pp2.skor_purata
FROM daftar_sst ds
JOIN penilaian_prestasi pp1 ON ds.id = pp1.daftar_sst_id
JOIN penilaian_prestasi pp2 ON ds.id = pp2.daftar_sst_id
WHERE pp1.skor_purata < 60
  AND pp2.skor_purata < 60
  AND pp1.bulan_penilaian = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
  AND pp2.bulan_penilaian = MONTH(DATE_SUB(CURDATE(), INTERVAL 2 MONTH))
  AND pp1.tahun_penilaian = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
  AND pp2.tahun_penilaian = YEAR(DATE_SUB(CURDATE(), INTERVAL 2 MONTH))
  AND ds.deleted_at IS NULL;
```

**Penerima:** Ketua Unit + Pengarah

**Saluran:** E-mel

**Action Required:**
- Review pembekal performance
- Consider termination or warning letter
- Document in activity log

---

## Validation Rules

### SST Registration Form

#### **Field Validations**

```php
// FR-M2-001 through FR-M2-011

'no_rujukan_sst' => [
    'required',
    'string',
    'max:50',
    'unique:daftar_sst,no_rujukan_sst',
    'regex:/^[A-Z0-9\/\-]+$/', // Format: SUK/KONTRAK/2026/001
],

'tarikh_sst' => [
    'required',
    'date',
    'before_or_equal:today',
],

'jabatan_kod' => [
    'required',
    'exists:jabatan,kod_jabatan',
],

'seksyen_unit_id' => [
    'required',
    'exists:seksyen_unit,id',
],

'pic_id' => [
    'required',
    'exists:users,id',
],

'pembekal_no_daftar' => [
    'required',
    'exists:pembekal,no_pendaftaran',
],

'skop' => [
    'required',
    'in:bekalan,perkhidmatan,kerja',
],

'kaedah_perolehan_id' => [
    'required',
    'exists:kaedah_perolehan,id',
],

'tajuk_perjanjian' => [
    'required',
    'string',
    'max:500',
],

'nilai_kontrak' => [
    'required',
    'numeric',
    'min:0',
    'max:999999999.99',
],

'tarikh_mula' => [
    'required',
    'date',
    'after_or_equal:tarikh_sst',
],

'tarikh_tamat' => [
    'required',
    'date',
    'after:tarikh_mula',
],

'tempoh_kontrak_bulan' => [
    'nullable',
    'integer',
    'min:1',
    'max:120', // Max 10 years
],
```

---

### Bon Pelaksanaan Form

#### **Field Validations**

```php
// FR-M4-001 through FR-M4-008

'no_rujukan_bon' => [
    'required',
    'string',
    'max:100',
    'unique:bon_pelaksanaan,no_rujukan_bon',
],

'nilai_bon' => [
    'required',
    'numeric',
    'min:0',
    function ($attribute, $value, $fail) use ($daftar_sst) {
        // Typically 5-10% of contract value
        $kontrak_nilai = $daftar_sst->nilai_kontrak;
        if ($value > $kontrak_nilai) {
            $fail('Nilai bon tidak boleh melebihi nilai kontrak.');
        }
    },
],

'tarikh_mula_bon' => [
    'required',
    'date',
    'before_or_equal:tarikh_tamat_bon',
],

'tarikh_tamat_bon' => [
    'required',
    'date',
    'after:tarikh_mula_bon',
    function ($attribute, $value, $fail) use ($daftar_sst) {
        // RULE: Bon must cover at least contract end date
        if ($value < $daftar_sst->tarikh_tamat) {
            $fail('Tarikh tamat bon mesti meliputi tarikh tamat kontrak.');
            // Trigger ALR-014
        }
    },
],

// RULE: Mutually Exclusive - Bon OR Insurans
'jenis_bon' => [
    'required',
    'in:jaminan_bank,insurans',
    function ($attribute, $value, $fail) use ($daftar_sst_id) {
        if ($value === 'jaminan_bank') {
            // Check no insurans exists
            if (InsuransKontrak::where('daftar_sst_id', $daftar_sst_id)->exists()) {
                $fail('Kontrak ini sudah mempunyai insurans. Tidak boleh tambah bon pelaksanaan.');
            }
        }
    },
],
```

---

### Penilaian Prestasi Form

#### **Field Validations**

```php
// FR-M5-001 through FR-M5-006

'bulan_penilaian' => [
    'required',
    'integer',
    'min:1',
    'max:12',
],

'tahun_penilaian' => [
    'required',
    'integer',
    'min:2020',
    'max:2050',
],

// Unique constraint
['daftar_sst_id', 'bulan_penilaian', 'tahun_penilaian'] => [
    'unique:penilaian_prestasi',
],

'skor_kualiti' => [
    'required',
    'numeric',
    'min:0',
    'max:100',
],

'skor_masa' => [
    'required',
    'numeric',
    'min:0',
    'max:100',
],

// ... (similar for other skor fields)

'ulasan_pic' => [
    'required',
    'string',
    'min:50', // Minimum 50 characters for meaningful feedback
],
```

**Auto-calculation:**
```php
$skor_purata = ($skor_kualiti + $skor_masa + $skor_kos + $skor_keselamatan + $skor_perkhidmatan) / 5;

$gred = match(true) {
    $skor_purata >= 90 => 'A',
    $skor_purata >= 80 => 'B',
    $skor_purata >= 70 => 'C',
    $skor_purata >= 60 => 'D',
    default => 'E'
};
```

---

### Lanjutan Tempoh Form

#### **Field Validations**

```php
'no_lanjutan' => [
    'required',
    'integer',
    'in:1,2', // Maximum 2 lanjutan
],

'tarikh_lanjutan_baharu' => [
    'required',
    'date',
    'after:' . $daftar_sst->tarikh_tamat,
],

'tempoh_tambahan_bulan' => [
    'required',
    'integer',
    'min:1',
    'max:24', // Max 2 years extension
],

'sebab_lanjutan' => [
    'required',
    'string',
    'min:100', // Minimum justification
],

// Constraint: Maximum 2 lanjutan
function ($attribute, $value, $fail) use ($daftar_sst_id) {
    $count = LanjutanTempoh::where('daftar_sst_id', $daftar_sst_id)->count();
    if ($count >= 2) {
        $fail('Maksimum 2 lanjutan sahaja dibenarkan.');
    }
}
```

---

### Lampiran Dokumen Upload

#### **File Validations**

```php
'file' => [
    'required',
    'file',
    'mimes:pdf,jpg,jpeg,png', // Only PDF and images
    'max:10240', // Max 10MB per file
],

'jenis_dokumen' => [
    'required',
    'in:sst,kontrak,bon,penilaian,lain',
],

'keterangan' => [
    'nullable',
    'string',
    'max:500',
],
```

**File Naming Convention:**
```php
$filename = sprintf(
    '%s_%s_%s.%s',
    $daftar_sst->no_rujukan_sst,
    $jenis_dokumen,
    now()->format('YmdHis'),
    $file->extension()
);
// Example: SUK-KONTRAK-2026-001_bon_20260512143000.pdf
```

---

## Workflow & Approval Rules

### Workflow 1: Pendaftaran Pengguna Baharu

**Flow:**
```
[PIC Cipta Akaun]
    ↓
[Auto-fill dari API EPSM via IC]
    ↓
[Email Verification Required]
    ↓
[Admin Semak & Tetapkan Role]
    ↓
[Pengarah Approve Role Assignment]
    ↓
[User Activated]
```

**Rules:**
1. `email_verified_at` MUST NOT be NULL before login allowed
2. User cannot access system until role assigned
3. Role assignment requires Pengarah approval
4. Default status: `is_active = FALSE` until approved

---

### Workflow 2: Daftar SST

**Flow:**
```
[PIC Isi Form SST]
    ↓
[Auto-populate Pembekal dari API iDaftar]
    ↓
[Validate nilai_kontrak]
    ↓ (if > RM 200k)
[REQUIRE Bon Pelaksanaan]
    ↓
[Save SST]
    ↓
[Auto-create daftar_kontrak record if kontrak_formal = TRUE]
    ↓
[Trigger Alert Engine Check]
```

**Rules:**
1. Pembekal must exist in `pembekal` table (cached from API)
2. If nilai_kontrak > RM 200,000 → Bon MUST be registered
3. If tempoh_kontrak_bulan > 4 → kontrak_formal = TRUE
4. PIC can only create SST for their own unit (unless granted permission)

---

### Workflow 3: Penilaian Prestasi Approval

**Flow:**
```
[PIC Isi Penilaian]
    ↓
[status_penilaian = 'deraf']
    ↓
[PIC Submit → status = 'hantar']
    ↓
[Notification to Ketua Unit]
    ↓
[Ketua Unit Review]
    ↓
    ├─ [LULUS] → status = 'lulus', Generate PDF
    └─ [TOLAK] → status = 'deraf', Notification to PIC with reason
```

**Rules:**
1. PIC can only edit penilaian in status 'deraf' or 'tolak'
2. Ketua Unit can only approve penilaian for their own unit
3. PDF only generated after approval
4. If skor_purata < 60% for 2 consecutive months → Trigger ALR-032

---

### Workflow 4: Kontrak Formal (Bernilai > RM 500,000)

**Flow:**
```
[PIC Daftar SST]
    ↓ (if nilai_kontrak > RM 500,000)
[REQUIRE Ketua Unit Review]
    ↓
[Ketua Unit Approve]
    ↓
[REQUIRE Pengarah Bahagian Approval]
    ↓
[Pengarah Approve]
    ↓
[Notification to Setiausaha Kerajaan (info only)]
    ↓
[Kontrak Aktif]
```

**Rules:**
1. Multi-level approval for high-value contracts
2. Cannot proceed to 'siap' status without all approvals
3. Audit trail for all approval steps

---

## RBAC & Permission Rules

### Permission Format

**Naming Convention:** `<resource>.<action>`

**Examples:**
```
sst.create
sst.read
sst.update
sst.delete
sst.approve

kontrak.create
kontrak.read
kontrak.update
kontrak.delete
kontrak.export

bon.create
bon.read
bon.update
bon.delete

dashboard.view
dashboard.view_executive

laporan.view
laporan.create
laporan.export

users.manage
roles.manage
permissions.manage

audit.view
audit.export
```

---

### Built-in Roles & Permissions

#### **1. super-admin**

**Slug:** `super-admin`

**Permissions:** ALL (wildcard `*`)

**Scope:** All departments, all units

**Count:** 1-2 users maximum

---

#### **2. admin**

**Slug:** `admin`

**Permissions:**
```
users.manage
roles.manage (create custom roles only)
permissions.view
master_data.manage
sst.read
kontrak.read
bon.read
dashboard.view
laporan.view
audit.view
```

**Scope:** All departments (read-only for transaction data)

**Restrictions:**
- Cannot modify transaction data (SST, kontrak, bon)
- Cannot assign super-admin role
- Cannot delete audit logs

---

#### **3. sk-exec** (Setiausaha Kerajaan & Timbalan)

**Slug:** `sk-exec`

**Permissions:**
```
dashboard.view_executive
dashboard.view
sst.read
kontrak.read
bon.read
penilaian.read
laporan.view
laporan.create
laporan.export
notifications.read
```

**Scope:** ALL departments (read-only across entire system)

**Purpose:** Strategic oversight, executive reporting

---

#### **4. pengarah**

**Slug:** `pengarah`

**Permissions:**
```
dashboard.view
sst.*  (full CRUD for own department)
kontrak.*
kontrak.approve (for > RM 500k)
bon.*
penilaian.approve
penilaian.read
laporan.*
notifications.*
```

**Scope:** Own department only (`jabatan_kod`)

**Special Powers:**
- Approve high-value contracts (> RM 500,000)
- Approve/reject penilaian prestasi
- Set kontrak_formal flag
- View all unit data within department

---

#### **5. ketua-unit**

**Slug:** `ketua-unit`

**Permissions:**
```
dashboard.view
sst.*  (CRUD for own unit)
kontrak.*
kontrak.approve (for < RM 500k within unit)
bon.*
penilaian.approve (unit level)
penilaian.read
laporan.view
laporan.export
notifications.*
```

**Scope:** Own unit only (`seksyen_unit_id`)

**Special Powers:**
- Approve penilaian prestasi for unit
- Approve kontrak formal < RM 500k
- Grant PIC access to other SSTs within unit

---

#### **6. pic** (Pegawai Perolehan)

**Slug:** `pic`

**Permissions:**
```
dashboard.view
sst.create
sst.read (own only)
sst.update (own only)
kontrak.create
kontrak.update (own only)
bon.create
bon.update (own only)
penilaian.create
penilaian.update (own drafts only)
lampiran.upload
notifications.read
```

**Scope:** Own created SSTs only (`created_by` or `pic_id`)

**Restrictions:**
- Cannot delete any records (only admin/super-admin)
- Cannot approve penilaian (only create/submit)
- Cannot view other PICs' SSTs unless granted

**Primary Alert Recipient:** YES (receives most alerts)

---

#### **7. audit**

**Slug:** `audit`

**Permissions:**
```
dashboard.view
sst.read
kontrak.read
bon.read
penilaian.read
audit.view
audit.export
activity_log.view
login_history.view
laporan.view
laporan.export
```

**Scope:** ALL departments (read-only across entire system)

**Special Access:**
- Full audit trail access
- Activity log access
- Login history access
- Generate audit reports

**Restrictions:**
- Absolutely NO write/update/delete permissions
- Cannot modify any data
- Cannot create notifications

---

### Permission Enforcement

**Filament Resources:**
```php
// In Resource class
public static function canCreate(): bool
{
    return auth()->user()->can('sst.create');
}

public static function canEdit(Model $record): bool
{
    $user = auth()->user();

    // PIC can only edit own records
    if ($user->hasRole('pic')) {
        return $record->pic_id === $user->id
            && auth()->user()->can('sst.update');
    }

    return auth()->user()->can('sst.update');
}

public static function canDelete(Model $record): bool
{
    // Only admin and super-admin can delete
    return auth()->user()->hasAnyRole(['admin', 'super-admin']);
}
```

---

## Data Scoping Rules

### Row-Level Security

**Implementation via Eloquent Global Scopes:**

```php
// app/Models/Scopes/DepartmentScope.php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();

        // super-admin, admin, sk-exec, audit = no scoping
        if ($user->hasAnyRole(['super-admin', 'admin', 'sk-exec', 'audit'])) {
            return;
        }

        // pengarah = own department only
        if ($user->hasRole('pengarah')) {
            $builder->where('jabatan_kod', $user->jabatan_kod);
            return;
        }

        // ketua-unit = own unit only
        if ($user->hasRole('ketua-unit')) {
            $builder->where('seksyen_unit_id', $user->seksyen_unit_id);
            return;
        }

        // pic = own records only
        if ($user->hasRole('pic')) {
            $builder->where(function($query) use ($user) {
                $query->where('pic_id', $user->id)
                      ->orWhere('created_by', $user->id);
            });
            return;
        }
    }
}
```

**Apply to Models:**
```php
// app/Models/DaftarSst.php
protected static function booted()
{
    static::addGlobalScope(new DepartmentScope);
}
```

---

### Scoping Matrix

| Role | Scope | SQL Filter |
|---|---|---|
| `super-admin` | ALL | None |
| `admin` | ALL | None |
| `sk-exec` | ALL | None |
| `audit` | ALL | None |
| `pengarah` | Department | `WHERE jabatan_kod = {user.jabatan_kod}` |
| `ketua-unit` | Unit | `WHERE seksyen_unit_id = {user.seksyen_unit_id}` |
| `pic` | Own records | `WHERE pic_id = {user.id} OR created_by = {user.id}` |

---

## Calculation & Auto-computation

### 1. Kategori Risiko Auto-calculation

**Run:** Daily at 8:00 AM via scheduler

```php
// app/Console/Commands/UpdateKategoriRisiko.php

// Update Kategori 1
DB::table('daftar_kontrak')
    ->join('daftar_sst', 'daftar_kontrak.daftar_sst_id', '=', 'daftar_sst.id')
    ->whereNull('daftar_kontrak.tarikh_deraf_ke_puu')
    ->whereNotNull('daftar_sst.tarikh_sst')
    ->where('daftar_sst.kontrak_formal', true)
    ->where('daftar_sst.status', 'aktif')
    ->whereRaw('DATEDIFF(daftar_sst.tarikh_tamat, CURDATE()) <= 180')
    ->whereRaw('DATEDIFF(daftar_sst.tarikh_tamat, CURDATE()) > 0')
    ->update(['daftar_kontrak.is_kategori_1' => true]);

// Update Kategori 2
DB::table('daftar_kontrak')
    ->join('daftar_sst', 'daftar_kontrak.daftar_sst_id', '=', 'daftar_sst.id')
    ->whereNull('daftar_kontrak.tarikh_deraf_ke_puu')
    ->whereNotNull('daftar_sst.tarikh_sst')
    ->where('daftar_sst.kontrak_formal', true)
    ->where('daftar_sst.status', 'aktif')
    ->whereRaw('DATEDIFF(CURDATE(), daftar_sst.tarikh_sst) >= 120')
    ->update(['daftar_kontrak.is_kategori_2' => true]);
```

---

### 2. Bon Status Auto-update

**Run:** Daily at 8:00 AM via scheduler

```php
// Update bon status based on tarikh_tamat_bon
DB::table('bon_pelaksanaan')
    ->whereNull('deleted_at')
    ->update([
        'status_bon' => DB::raw("
            CASE
                WHEN DATEDIFF(tarikh_tamat_bon, CURDATE()) <= 0 THEN 'tamat'
                WHEN DATEDIFF(tarikh_tamat_bon, CURDATE()) <= 180 THEN 'akan_tamat'
                WHEN status_bon IN ('serah_balik', 'dalam_simpanan') THEN status_bon
                ELSE 'aktif'
            END
        ")
    ]);
```

---

### 3. Penilaian Skor Purata

**Auto-calculate on save:**

```php
// app/Models/PenilaianPrestasi.php

protected static function booted()
{
    static::saving(function ($penilaian) {
        // Calculate average score
        $penilaian->skor_purata = (
            $penilaian->skor_kualiti +
            $penilaian->skor_masa +
            $penilaian->skor_kos +
            $penilaian->skor_keselamatan +
            $penilaian->skor_perkhidmatan
        ) / 5;

        // Assign grade
        $penilaian->gred = match(true) {
            $penilaian->skor_purata >= 90 => 'A',
            $penilaian->skor_purata >= 80 => 'B',
            $penilaian->skor_purata >= 70 => 'C',
            $penilaian->skor_purata >= 60 => 'D',
            default => 'E'
        };
    });
}
```

---

### 4. Kontrak Formal Flag

**Auto-set on SST creation:**

```php
// If tempoh > 4 bulan, auto-set kontrak_formal = TRUE
if ($daftar_sst->tempoh_kontrak_bulan > 4) {
    $daftar_sst->kontrak_formal = true;
}
```

---

### 5. Bon Tarikh Validation

**Auto-check on save:**

```php
// app/Models/BonPelaksanaan.php

protected static function booted()
{
    static::saving(function ($bon) {
        $sst = $bon->daftarSst;

        // Check if bon covers contract period
        if ($bon->tarikh_tamat_bon < $sst->tarikh_tamat) {
            $bon->is_tarikh_valid = false;

            // Queue alert ALR-014
            dispatch(new SendAlertJob('ALR-014', $sst->id));
        } else {
            $bon->is_tarikh_valid = true;
        }
    });
}
```

---

## Integration Rules

### API iDaftar (Supplier Data)

**Endpoint:** `https://idaftar.gov.my/api/pembekal?no_pendaftaran={NO}`

**Caching Strategy:**
```
1. Check if pembekal exists in local database
2. If exists AND cached_at < 7 days ago → Use cached data
3. If not exists OR cache expired → Call API
4. Store/update in pembekal table
5. Set cached_at = NOW()
```

**Fallback:**
```
IF API fails THEN
    Use cached data (even if expired)
    Log error
    Notify admin if API down > 30 minutes
END IF
```

**Implementation:**
```php
// app/Services/IDaftarService.php

public function getPembekal(string $no_pendaftaran): ?Pembekal
{
    // Check cache
    $pembekal = Pembekal::where('no_pendaftaran', $no_pendaftaran)
        ->where('cached_at', '>=', now()->subDays(7))
        ->first();

    if ($pembekal) {
        return $pembekal; // Use cached
    }

    // Call API
    try {
        $response = Http::timeout(10)
            ->retry(3, 1000) // Retry 3 times with 1s delay
            ->get("https://idaftar.gov.my/api/pembekal", [
                'no_pendaftaran' => $no_pendaftaran,
                'secret_key' => config('services.idaftar.key'),
            ]);

        if ($response->successful()) {
            $data = $response->json();

            // Update or create
            return Pembekal::updateOrCreate(
                ['no_pendaftaran' => $no_pendaftaran],
                [
                    'nama_syarikat' => $data['nama'],
                    'kategori' => $data['kategori'],
                    'status_pendaftaran' => $data['status'],
                    'cached_at' => now(),
                ]
            );
        }
    } catch (\Exception $e) {
        Log::error('iDaftar API failed', ['error' => $e->getMessage()]);

        // Return stale cache if exists
        return Pembekal::where('no_pendaftaran', $no_pendaftaran)->first();
    }

    return null;
}
```

---

### API EPSM (Employee Data)

**Endpoint:** `https://epsm.kedah.gov.my/api_kuarters.php?secret_key={KEY}&no_kp={IC}`

**Usage:** User registration only

**Caching:** No caching (fresh data on registration)

**Implementation:**
```php
public function getUserDataFromEPSM(string $no_ic): ?array
{
    try {
        $response = Http::timeout(10)
            ->get("https://epsm.kedah.gov.my/api_kuarters.php", [
                'secret_key' => config('services.epsm.key'),
                'no_kp' => $no_ic,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'name' => $data['nama'],
                'email' => $data['emel'],
                'jabatan_kod' => $data['kod_jabatan'],
                'jawatan' => $data['jawatan'],
                'no_telefon' => $data['telefon'],
            ];
        }
    } catch (\Exception $e) {
        Log::error('EPSM API failed', ['error' => $e->getMessage()]);
    }

    return null;
}
```

---

## Security & Compliance Rules

### Password Policy

```php
// FR-M1-003, FR-M1-006, NFR-S-008

'password' => [
    'required',
    'string',
    'min:8',
    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', // Must have upper, lower, number, symbol
    'different:email', // Cannot be same as email
    new NotInPasswordHistory($user->id, 5), // Cannot reuse last 5 passwords
],

// Password expiry: 90 days
if ($user->password_changed_at <= now()->subDays(90)) {
    $user->must_change_password = true;
}
```

---

### Account Lockout

```php
// FR-M1-005

// After 5 failed attempts
if ($failed_attempts >= 5) {
    $user->is_active = false;
    $user->save();

    // Notify admin
    Mail::to(config('admin.email'))->send(new AccountLockedNotification($user));

    throw new AccountLockedException('Akaun dikunci selepas 5 percubaan gagal.');
}
```

---

### Session Timeout

```php
// FR-M1-008

// config/session.php
'lifetime' => 30, // 30 minutes
'expire_on_close' => false,

// Middleware checks last activity
if (session('last_activity') < now()->subMinutes(30)) {
    auth()->logout();
    session()->flush();
    redirect('/login')->with('message', 'Sesi tamat. Sila log masuk semula.');
}
```

---

### Two-Factor Authentication

```php
// FR-M1-004

// Mandatory for all roles
// Using Laravel Fortify + TOTP

// On login:
if ($user->two_factor_secret && !session('2fa_verified')) {
    return redirect()->route('two-factor.challenge');
}
```

---

### Data Protection (PDPA Compliance)

**Personal Data Fields:**
```
- users.name
- users.email
- users.no_kad_pengenalan
- users.no_telefon
- pembekal_pic_nama
- pembekal_pic_emel
- pembekal_pic_telefon
```

**Rules:**
1. Encrypt sensitive fields at rest (AES-256)
2. Obtain user consent on registration
3. Allow users to request data export (GDPR-style)
4. Allow users to request data deletion (with caveats for legal requirements)
5. Log all access to personal data in audit trail

**Implementation:**
```php
// app/Models/User.php

protected $casts = [
    'no_kad_pengenalan' => 'encrypted',
];
```

---

## Audit Trail Rules

### What to Audit

**All CRUD operations on:**
- daftar_sst
- daftar_kontrak
- bon_pelaksanaan
- insurans_kontrak
- penilaian_prestasi
- users
- roles
- permissions

**Audit Fields Captured:**
```
- auditable_type (Model class)
- auditable_id (Model ID)
- event (created/updated/deleted)
- user_id (Who performed)
- old_values (JSON - before)
- new_values (JSON - after)
- url (Request URL)
- ip_address (User IP)
- user_agent (Browser/Device)
- created_at (Timestamp)
```

**Implementation:**
```php
// app/Models/DaftarSst.php

use OwenIt\Auditing\Contracts\Auditable;

class DaftarSst extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'no_rujukan_sst',
        'nilai_kontrak',
        'status',
        'pic_id',
        // ... all important fields
    ];

    protected $auditExclude = [
        'created_at',
        'updated_at',
    ];
}
```

---

### Audit Retention

**Retention Period:** 7 years (compliance requirement)

**Archival Strategy:**
```
1. Keep 2 years in main database (fast access)
2. Archive 2-7 years to separate archival table
3. Compress old audits (JSON → gzip)
4. Off-site backup monthly
```

---

## Data Retention & Archival

### Retention Policies

| Data Type | Active Period | Archive Period | Permanent Delete |
|---|---|---|---|
| Transaction Data (SST, Kontrak, Bon) | Permanent | N/A | Never (soft delete only) |
| Audit Logs | 2 years | 5 years | After 7 years |
| Activity Logs | 2 years | N/A | After 2 years |
| Login History | 1 year | 1 year | After 2 years |
| Alert History | 1 year | N/A | After 1 year |
| Notifications | 6 months | N/A | After 6 months |

---

### Soft Delete Rules

**All transaction tables use soft delete:**
```sql
deleted_at TIMESTAMP NULL
```

**Rules:**
1. PIC/Ketua Unit cannot delete (only hide from view)
2. Admin can soft delete
3. Super-admin can hard delete after 90 days
4. Soft deleted records excluded from alerts and reports
5. Soft deleted records still visible in audit trail

**Implementation:**
```php
// All models use SoftDeletes trait
use Illuminate\Database\Eloquent\SoftDeletes;

class DaftarSst extends Model
{
    use SoftDeletes;
}

// Query: Auto-exclude soft deleted
DaftarSst::all(); // Only non-deleted

// Include soft deleted
DaftarSst::withTrashed()->get();

// Only soft deleted
DaftarSst::onlyTrashed()->get();

// Restore
$sst->restore();

// Force delete (super-admin only, 90+ days old)
if (auth()->user()->hasRole('super-admin')
    && $sst->deleted_at <= now()->subDays(90)) {
    $sst->forceDelete();
}
```

---

**Tamat Rules Document**

*Untuk rujukan pembangunan, lihat juga:*
- *PRD: prd_sistem_pengurusan_kontrak_suk_kedah.md*
- *ERD: erd.md*
- *CLAUDE.md: Development guidelines*
