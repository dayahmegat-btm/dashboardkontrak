<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class AlertRule extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'alert_rules';

    protected $fillable = [
        'kod_alert',
        'nama_alert',
        'penerangan',
        'trigger_type',
        'trigger_conditions',
        'days_before',
        'schedule',
        'recipient_roles',
        'recipient_emails',
        'email_subject',
        'email_body',
        'notification_message',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'recipient_roles' => 'array',
        'recipient_emails' => 'array',
        'is_active' => 'boolean',
        'days_before' => 'integer',
    ];

    // Relationships
    public function alertLogs(): HasMany
    {
        return $this->hasMany(AlertLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('trigger_type', $type);
    }

    public function scopeExpiry($query)
    {
        return $query->where('trigger_type', 'expiry');
    }
}
