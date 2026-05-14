<?php

namespace App\Models;

use App\Models\Scopes\DaftarSstRelationshipScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DaftarKontrak extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'daftar_kontrak';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new DaftarSstRelationshipScope);
    }

    protected $fillable = [
        'daftar_sst_id',
        'no_kontrak',
        'tarikh_kontrak',
        'tajuk',
        'penerangan',
        'nilai_kontrak',
        'tarikh_mula',
        'tarikh_tamat',
        'tarikh_lanjutan_1',
        'tarikh_lanjutan_2',
        'tempoh_bulan',
        'pembekal_id',
        'pegawai_pengawal',
        'pegawai_penyelia',
        'status_kontrak_id',
        'fail_kontrak_path',
        'tarikh_deraf_ke_puu',
        'tarikh_terima_dari_puu',
        'tarikh_tandatangan',
        'tarikh_stamping',
        'is_siap',
        'catatan_dalaman',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarikh_kontrak' => 'date',
        'tarikh_mula' => 'date',
        'tarikh_tamat' => 'date',
        'tarikh_lanjutan_1' => 'date',
        'tarikh_lanjutan_2' => 'date',
        'tarikh_deraf_ke_puu' => 'date',
        'tarikh_terima_dari_puu' => 'date',
        'tarikh_tandatangan' => 'date',
        'tarikh_stamping' => 'date',
        'nilai_kontrak' => 'decimal:2',
        'tempoh_bulan' => 'integer',
        'is_siap' => 'boolean',
    ];

    // Relationships
    public function daftarSst(): BelongsTo
    {
        return $this->belongsTo(DaftarSst::class);
    }

    public function pembekal(): BelongsTo
    {
        return $this->belongsTo(Pembekal::class);
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

    public function bonPelaksanaans(): HasMany
    {
        return $this->hasMany(BonPelaksanaan::class);
    }

    public function penilaianPrestasis(): HasMany
    {
        return $this->hasMany(PenilaianPrestasi::class);
    }

    public function aduans(): HasMany
    {
        return $this->hasMany(Aduan::class);
    }

    public function lanjutanTempohs(): HasMany
    {
        return $this->hasMany(LanjutanTempoh::class);
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
}
