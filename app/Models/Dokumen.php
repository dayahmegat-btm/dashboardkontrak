<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Dokumen extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'dokumen';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'nama_dokumen',
        'jenis_dokumen',
        'no_rujukan',
        'tarikh_dokumen',
        'nama_fail',
        'fail_path',
        'mime_type',
        'saiz_fail',
        'catatan',
        'uploaded_by',
    ];

    protected $casts = [
        'tarikh_dokumen' => 'date',
        'saiz_fail' => 'integer',
    ];

    // Relationships
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
