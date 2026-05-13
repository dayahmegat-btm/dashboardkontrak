<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class NotificationSetting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'notification_settings';

    protected $fillable = [
        'user_id',
        'email_enabled',
        'fcm_enabled',
        'database_enabled',
        'notify_contract_expiry',
        'notify_bond_expiry',
        'notify_kategori_1',
        'notify_kategori_2',
        'notify_high_commitment',
        'notify_complaints',
        'notify_performance_assessment',
        'email_frequency',
        'digest_time',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'fcm_enabled' => 'boolean',
        'database_enabled' => 'boolean',
        'notify_contract_expiry' => 'boolean',
        'notify_bond_expiry' => 'boolean',
        'notify_kategori_1' => 'boolean',
        'notify_kategori_2' => 'boolean',
        'notify_high_commitment' => 'boolean',
        'notify_complaints' => 'boolean',
        'notify_performance_assessment' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeEmailEnabled($query)
    {
        return $query->where('email_enabled', true);
    }

    public function scopeInstantNotifications($query)
    {
        return $query->where('email_frequency', 'instant');
    }

    public function scopeDailyDigest($query)
    {
        return $query->where('email_frequency', 'daily_digest');
    }

    public function scopeWeeklyDigest($query)
    {
        return $query->where('email_frequency', 'weekly_digest');
    }

    // Helper methods
    public function isNotificationEnabled(string $type): bool
    {
        $field = "notify_{$type}";

        return isset($this->$field) && $this->$field === true;
    }

    public function hasChannelEnabled(string $channel): bool
    {
        $field = "{$channel}_enabled";

        return isset($this->$field) && $this->$field === true;
    }
}
