<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pembekal extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pembekal';

    protected $fillable = [
        'nama_syarikat',
        'no_pendaftaran',
        'alamat',
        'no_telefon',
        'emel',
        'pic_nama',
        'pic_telefon',
        'pic_emel',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function daftarSsts(): HasMany
    {
        return $this->hasMany(DaftarSst::class);
    }

    public function daftarKontraks(): HasMany
    {
        return $this->hasMany(DaftarKontrak::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
