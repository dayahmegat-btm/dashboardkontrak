<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    use HasFactory;

    protected $table = 'system_logs';

    protected $fillable = [
        'level',
        'category',
        'event',
        'message',
        'context',
        'ip_address',
        'user_agent',
        'user_id',
        'logged_at',
    ];

    protected $casts = [
        'context' => 'array',
        'logged_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeDebug($query)
    {
        return $query->where('level', 'debug');
    }

    public function scopeInfo($query)
    {
        return $query->where('level', 'info');
    }

    public function scopeWarning($query)
    {
        return $query->where('level', 'warning');
    }

    public function scopeError($query)
    {
        return $query->where('level', 'error');
    }

    public function scopeCritical($query)
    {
        return $query->where('level', 'critical');
    }

    public function scopeAlert($query)
    {
        return $query->where('level', 'alert');
    }

    public function scopeEmergency($query)
    {
        return $query->where('level', 'emergency');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('logged_at', '>=', now()->subDays($days));
    }

    public function scopeAuth($query)
    {
        return $query->where('category', 'auth');
    }

    public function scopeQueue($query)
    {
        return $query->where('category', 'queue');
    }

    public function scopeBackup($query)
    {
        return $query->where('category', 'backup');
    }
}
