<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Jabatan extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'jabatan';

    protected $fillable = [
        'kod_jabatan',
        'nama_jabatan',
        'penerangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function seksyenUnits(): HasMany
    {
        return $this->hasMany(SeksyenUnit::class);
    }

    public function daftarSsts(): HasMany
    {
        return $this->hasMany(DaftarSst::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
