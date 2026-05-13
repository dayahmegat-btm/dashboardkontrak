<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Aduan extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'aduan';

    protected $fillable = [
        'daftar_kontrak_id',
        'no_aduan',
        'tarikh_aduan',
        'tajuk',
        'penerangan',
        'kategori',
        'keutamaan',
        'status',
        'pengadu_nama',
        'pengadu_jabatan',
        'pengadu_telefon',
        'pengadu_emel',
        'tindakan_diambil',
        'tarikh_tindakan',
        'tarikh_selesai',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarikh_aduan' => 'date',
        'tarikh_tindakan' => 'date',
        'tarikh_selesai' => 'date',
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

    public function lampirans(): MorphMany
    {
        return $this->morphMany(Lampiran::class, 'attachable');
    }

    // Scopes
    public function scopeBaru($query)
    {
        return $query->where('status', 'Baru');
    }

    public function scopeKritikal($query)
    {
        return $query->where('keutamaan', 'Kritikal');
    }

    public function scopeSelesai($query)
    {
        return $query->whereIn('status', ['Selesai', 'Ditutup']);
    }
}
