<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable, FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use HasApiTokens;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_kad_pengenalan',
        'no_telefon',
        'jabatan_id',
        'seksyen_unit_id',
        'jawatan',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'password_changed_at',
        'force_password_change',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'force_password_change' => 'boolean',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Check if user can access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasVerifiedEmail();
    }

    /**
     * Get the user's avatar URL
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    /**
     * Get user's full name with position
     */
    public function getFullNameWithPositionAttribute(): string
    {
        return $this->name . ($this->jawatan ? ' (' . $this->jawatan . ')' : '');
    }

    /**
     * Get user's department name
     */
    public function getDepartmentNameAttribute(): ?string
    {
        return $this->jabatan?->nama_jabatan;
    }

    /**
     * Relationships
     */
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function seksyenUnit(): BelongsTo
    {
        return $this->belongsTo(SeksyenUnit::class);
    }

    public function daftarSsts(): HasMany
    {
        return $this->hasMany(DaftarSst::class, 'created_by');
    }

    public function daftarKontraks(): HasMany
    {
        return $this->hasMany(DaftarKontrak::class, 'created_by');
    }

    public function bonPelaksanaans(): HasMany
    {
        return $this->hasMany(BonPelaksanaan::class, 'created_by');
    }

    public function penilaianPrestasis(): HasMany
    {
        return $this->hasMany(PenilaianPrestasi::class, 'created_by');
    }

    public function aduans(): HasMany
    {
        return $this->hasMany(Aduan::class, 'created_by');
    }

    public function notificationSettings(): HasMany
    {
        return $this->hasMany(NotificationSetting::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInDepartment($query, int $jabatanId)
    {
        return $query->where('jabatan_id', $jabatanId);
    }

    public function scopeInUnit($query, int $seksyenUnitId)
    {
        return $query->where('seksyen_unit_id', $seksyenUnitId);
    }

    /**
     * Check if password needs to be changed
     */
    public function needsPasswordChange(): bool
    {
        if ($this->force_password_change) {
            return true;
        }

        if (!$this->password_changed_at) {
            return false;
        }

        // Force password change every 90 days
        return $this->password_changed_at->addDays(90)->isPast();
    }

    /**
     * Record login
     */
    public function recordLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
    }

    /**
     * Update password changed timestamp
     */
    public function updatePasswordChangedAt(): void
    {
        $this->update([
            'password_changed_at' => now(),
            'force_password_change' => false,
        ]);
    }
}
