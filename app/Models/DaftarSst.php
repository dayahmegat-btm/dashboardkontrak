<?php

namespace App\Models;

use App\Models\Scopes\DepartmentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DaftarSst extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'daftar_sst';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new DepartmentScope);
    }

    protected $fillable = [
        'no_sst',
        'tajuk',
        'penerangan',
        'jabatan_id',
        'seksyen_unit_id',
        'pembekal_id',
        'kategori_perkhidmatan_id',
        'kaedah_perolehan_id',
        'status_kontrak_id',
        'tarikh_mula',
        'tarikh_tamat',
        'tempoh_bulan',
        'nilai_kontrak',
        'nilai_komitmen',
        'baki_kontrak',
        'pegawai_pengawal',
        'pegawai_penyelia',
        'is_kategori_1',
        'is_kategori_2',
        'hari_sehingga_tamat',
        'created_by',
        'updated_by',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'tarikh_mula' => 'date',
        'tarikh_tamat' => 'date',
        'nilai_kontrak' => 'decimal:2',
        'nilai_komitmen' => 'decimal:2',
        'baki_kontrak' => 'decimal:2',
        'is_kategori_1' => 'boolean',
        'is_kategori_2' => 'boolean',
        'tempoh_bulan' => 'integer',
        'hari_sehingga_tamat' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function seksyenUnit(): BelongsTo
    {
        return $this->belongsTo(SeksyenUnit::class);
    }

    public function pembekal(): BelongsTo
    {
        return $this->belongsTo(Pembekal::class);
    }

    public function kategoriPerkhidmatan(): BelongsTo
    {
        return $this->belongsTo(KategoriPerkhidmatan::class);
    }

    public function kaedahPerolehan(): BelongsTo
    {
        return $this->belongsTo(KaedahPerolehan::class);
    }

    public function statusKontrak(): BelongsTo
    {
        return $this->belongsTo(StatusKontrak::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function daftarKontraks(): HasMany
    {
        return $this->hasMany(DaftarKontrak::class);
    }

    public function dokumens(): MorphMany
    {
        return $this->morphMany(Dokumen::class, 'documentable');
    }

    public function catatans(): MorphMany
    {
        return $this->morphMany(Catatan::class, 'notable');
    }

    public function lampirans(): MorphMany
    {
        return $this->morphMany(Lampiran::class, 'attachable');
    }

    // Scopes
    public function scopeKategori1($query)
    {
        return $query->where('is_kategori_1', true);
    }

    public function scopeKategori2($query)
    {
        return $query->where('is_kategori_2', true);
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('hari_sehingga_tamat', '<=', $days)
            ->where('hari_sehingga_tamat', '>', 0);
    }
}
