<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class AlertLog extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'alert_logs';

    protected $fillable = [
        'alert_rule_id',
        'alertable_type',
        'alertable_id',
        'triggered_at',
        'trigger_data',
        'status',
        'recipients_sent',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'sent_at' => 'datetime',
        'trigger_data' => 'array',
        'recipients_sent' => 'array',
    ];

    // Relationships
    public function alertRule(): BelongsTo
    {
        return $this->belongsTo(AlertRule::class);
    }

    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('triggered_at', '>=', now()->subDays($days));
    }
}
