<?php

namespace App\Models;

use App\Models\Scopes\DaftarSstRelationshipScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class LanjutanTempoh extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'lanjutan_tempoh';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new DaftarSstRelationshipScope);
    }

    protected $fillable = [
        'daftar_kontrak_id',
        'no_lanjutan',
        'lanjutan_ke',
        'tarikh_mula_asal',
        'tarikh_tamat_asal',
        'tarikh_mula_baru',
        'tarikh_tamat_baru',
        'tempoh_lanjutan_bulan',
        'sebab_lanjutan',
        'justifikasi',
        'nilai_kontrak_asal',
        'nilai_tambahan',
        'nilai_kontrak_baru',
        'fail_surat_lanjutan',
        'status_kontrak_id',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarikh_mula_asal' => 'date',
        'tarikh_tamat_asal' => 'date',
        'tarikh_mula_baru' => 'date',
        'tarikh_tamat_baru' => 'date',
        'tempoh_lanjutan_bulan' => 'integer',
        'lanjutan_ke' => 'integer',
        'nilai_kontrak_asal' => 'decimal:2',
        'nilai_tambahan' => 'decimal:2',
        'nilai_kontrak_baru' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function daftarKontrak(): BelongsTo
    {
        return $this->belongsTo(DaftarKontrak::class);
    }

    public function statusKontrak(): BelongsTo
    {
        return $this->belongsTo(StatusKontrak::class);
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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
