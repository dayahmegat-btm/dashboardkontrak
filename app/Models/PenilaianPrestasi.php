<?php

namespace App\Models;

use App\Models\Scopes\DaftarSstRelationshipScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PenilaianPrestasi extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'penilaian_prestasi';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new DaftarSstRelationshipScope);
    }

    protected $fillable = [
        'daftar_kontrak_id',
        'tarikh_penilaian',
        'tempoh_penilaian',
        'skor_kualiti',
        'skor_masa',
        'skor_kos',
        'skor_keselamatan',
        'skor_keseluruhan',
        'ulasan',
        'cadangan_penambahbaikan',
        'gred',
        'fail_penilaian_path',
        'dinilai_oleh',
        'jawatan_penilai',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarikh_penilaian' => 'date',
        'skor_kualiti' => 'integer',
        'skor_masa' => 'integer',
        'skor_kos' => 'integer',
        'skor_keselamatan' => 'integer',
        'skor_keseluruhan' => 'decimal:2',
    ];

    // Relationships
    public function daftarKontrak(): BelongsTo
    {
        return $this->belongsTo(DaftarKontrak::class);
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

    // Scopes
    public function scopeGredA($query)
    {
        return $query->where('gred', 'A');
    }

    public function scopePoorPerformance($query)
    {
        return $query->whereIn('gred', ['D', 'E']);
    }
}
