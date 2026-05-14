<?php

namespace App\Models;

use App\Models\Scopes\DaftarSstRelationshipScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BonPelaksanaan extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bon_pelaksanaan';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new DaftarSstRelationshipScope);
    }

    protected $fillable = [
        'daftar_kontrak_id',
        'jenis_bon_id',
        'no_bon',
        'nilai_bon',
        'tarikh_mula',
        'tarikh_tamat',
        'institusi_penjamin',
        'fail_bon_path',
        'status',
        'hari_sehingga_tamat',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarikh_mula' => 'date',
        'tarikh_tamat' => 'date',
        'nilai_bon' => 'decimal:2',
        'hari_sehingga_tamat' => 'integer',
    ];

    // Relationships
    public function daftarKontrak(): BelongsTo
    {
        return $this->belongsTo(DaftarKontrak::class);
    }

    public function jenisBon(): BelongsTo
    {
        return $this->belongsTo(JenisBon::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function lampirans(): MorphMany
    {
        return $this->morphMany(Lampiran::class, 'attachable');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('hari_sehingga_tamat', '<=', $days)
            ->where('hari_sehingga_tamat', '>', 0)
            ->where('status', 'Aktif');
    }
}
