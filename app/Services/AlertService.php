<?php

namespace App\Services;

use App\Models\AlertLog;
use App\Models\AlertRule;
use App\Models\BonPelaksanaan;
use App\Models\DaftarKontrak;
use App\Models\DaftarSst;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class AlertService
{
    /**
     * Check all active alert rules and trigger notifications
     */
    public function checkAndTriggerAlerts(): array
    {
        $results = [
            'checked' => 0,
            'triggered' => 0,
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $activeRules = AlertRule::active()->get();
            $results['checked'] = $activeRules->count();

            foreach ($activeRules as $rule) {
                try {
                    $triggered = $this->checkAlertRule($rule);
                    $results['triggered'] += $triggered;
                } catch (\Exception $e) {
                    $results['errors'][] = "Rule {$rule->kod_alert}: {$e->getMessage()}";
                    Log::error('Alert rule check failed', [
                        'rule_id' => $rule->id,
                        'kod_alert' => $rule->kod_alert,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Alert check failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Check a specific alert rule and trigger if conditions met
     */
    protected function checkAlertRule(AlertRule $rule): int
    {
        $triggered = 0;

        switch ($rule->trigger_type) {
            case 'kategori_1_contract':
                $triggered = $this->checkKategori1Contracts($rule);
                break;

            case 'kategori_2_contract':
                $triggered = $this->checkKategori2Contracts($rule);
                break;

            case 'bond_expiry':
                $triggered = $this->checkBondExpiry($rule);
                break;

            case 'bond_return':
                $triggered = $this->checkBondReturn($rule);
                break;

            case 'performance_evaluation':
                $triggered = $this->checkPerformanceEvaluation($rule);
                break;

            default:
                Log::warning('Unknown alert trigger type', [
                    'rule_id' => $rule->id,
                    'trigger_type' => $rule->trigger_type,
                ]);
        }

        return $triggered;
    }

    /**
     * Check Kategori 1 Contracts: SST issued, draft not sent to PUU, contract ending in 6 months
     */
    protected function checkKategori1Contracts(AlertRule $rule): int
    {
        $sixMonthsFromNow = Carbon::now()->addMonths(6);
        $triggered = 0;

        // Find contracts with SST, no draft to PUU, and ending within 6 months
        $contracts = DaftarKontrak::whereHas('daftarSst')
            ->whereNull('tarikh_deraf_ke_puu')
            ->whereDate('tarikh_tamat', '<=', $sixMonthsFromNow)
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->get();

        foreach ($contracts as $contract) {
            if ($this->shouldTriggerAlert($rule, $contract, 'kategori_1_contract')) {
                $this->triggerAlert($rule, $contract, [
                    'no_kontrak' => $contract->no_kontrak,
                    'no_sst' => $contract->daftarSst->no_sst ?? 'N/A',
                    'tarikh_tamat' => $contract->tarikh_tamat->format('d/m/Y'),
                    'days_until_expiry' => Carbon::now()->diffInDays($contract->tarikh_tamat),
                    'pembekal' => $contract->daftarSst->pembekal->nama_syarikat ?? 'N/A',
                ]);
                $triggered++;
            }
        }

        return $triggered;
    }

    /**
     * Check Kategori 2 Contracts: SST issued, no draft to PUU after 4 months
     */
    protected function checkKategori2Contracts(AlertRule $rule): int
    {
        $fourMonthsAgo = Carbon::now()->subMonths(4);
        $triggered = 0;

        // Find contracts where SST was registered 4+ months ago and no draft sent to PUU
        $contracts = DaftarKontrak::whereHas('daftarSst', function ($query) use ($fourMonthsAgo) {
                $query->whereDate('created_at', '<=', $fourMonthsAgo);
            })
            ->whereNull('tarikh_deraf_ke_puu')
            ->get();

        foreach ($contracts as $contract) {
            if ($this->shouldTriggerAlert($rule, $contract, 'kategori_2_contract')) {
                $this->triggerAlert($rule, $contract, [
                    'no_kontrak' => $contract->no_kontrak,
                    'no_sst' => $contract->daftarSst->no_sst ?? 'N/A',
                    'tarikh_sst' => $contract->daftarSst->created_at?->format('d/m/Y') ?? 'N/A',
                    'months_since_sst' => $contract->daftarSst->created_at ? Carbon::parse($contract->daftarSst->created_at)->diffInMonths(Carbon::now()) : 0,
                    'pembekal' => $contract->daftarSst->pembekal->nama_syarikat ?? 'N/A',
                ]);
                $triggered++;
            }
        }

        return $triggered;
    }

    /**
     * Check Performance Bond Expiry: Alerts at 180, 90, 30, 7 days before expiry
     */
    protected function checkBondExpiry(AlertRule $rule): int
    {
        $daysThreshold = $rule->days_before ?? 30;
        $triggered = 0;

        $bonds = BonPelaksanaan::where('status', 'aktif')
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->whereDate('tarikh_tamat', '<=', Carbon::now()->addDays($daysThreshold))
            ->get();

        foreach ($bonds as $bond) {
            if ($this->shouldTriggerAlert($rule, $bond, 'bond_expiry')) {
                $daysUntilExpiry = Carbon::now()->diffInDays($bond->tarikh_tamat);

                $this->triggerAlert($rule, $bond, [
                    'no_bon' => $bond->no_bon,
                    'no_kontrak' => $bond->daftarKontrak->no_kontrak ?? 'N/A',
                    'jenis_bon' => $bond->jenisBon->nama ?? 'N/A',
                    'tarikh_tamat' => $bond->tarikh_tamat->format('d/m/Y'),
                    'days_until_expiry' => $daysUntilExpiry,
                    'nilai_bon' => number_format($bond->nilai_bon, 2),
                    'institusi' => $bond->institusi_penjamin,
                ]);
                $triggered++;
            }
        }

        return $triggered;
    }

    /**
     * Check Bond Return: Escalating alerts 30/60/90 days after contract completion
     */
    protected function checkBondReturn(AlertRule $rule): int
    {
        $triggered = 0;

        // Find bonds for completed contracts that haven't been returned
        $bonds = BonPelaksanaan::whereHas('daftarKontrak', function ($query) {
                $query->where('is_siap', true)
                    ->whereNotNull('tarikh_stamping');
            })
            ->where('status', 'aktif')
            ->get();

        foreach ($bonds as $bond) {
            $contract = $bond->daftarKontrak;
            $daysAfterCompletion = Carbon::parse($contract->tarikh_stamping)->diffInDays(Carbon::now());

            // Alert at 30, 60, 90 days after completion
            if (in_array($daysAfterCompletion, [30, 60, 90]) || $daysAfterCompletion > 90) {
                if ($this->shouldTriggerAlert($rule, $bond, 'bond_return')) {
                    $this->triggerAlert($rule, $bond, [
                        'no_bon' => $bond->no_bon,
                        'no_kontrak' => $contract->no_kontrak,
                        'tarikh_siap' => Carbon::parse($contract->tarikh_stamping)->format('d/m/Y'),
                        'days_after_completion' => $daysAfterCompletion,
                        'nilai_bon' => number_format($bond->nilai_bon, 2),
                        'institusi' => $bond->institusi_penjamin,
                    ]);
                    $triggered++;
                }
            }
        }

        return $triggered;
    }

    /**
     * Check Performance Evaluation: Monthly reminders on 1st of month
     */
    protected function checkPerformanceEvaluation(AlertRule $rule): int
    {
        // Only run on 1st of month
        if (Carbon::now()->day !== 1) {
            return 0;
        }

        $triggered = 0;

        // Find active contracts that need performance evaluation
        $contracts = DaftarKontrak::where('is_siap', false)
            ->whereNotNull('tarikh_mula')
            ->whereDate('tarikh_mula', '<=', Carbon::now())
            ->get();

        foreach ($contracts as $contract) {
            // Check if evaluation done this month
            $hasEvaluationThisMonth = $contract->penilaianPrestasis()
                ->whereYear('tarikh_penilaian', Carbon::now()->year)
                ->whereMonth('tarikh_penilaian', Carbon::now()->month)
                ->exists();

            if (!$hasEvaluationThisMonth) {
                if ($this->shouldTriggerAlert($rule, $contract, 'performance_evaluation')) {
                    $this->triggerAlert($rule, $contract, [
                        'no_kontrak' => $contract->no_kontrak,
                        'no_sst' => $contract->daftarSst->no_sst ?? 'N/A',
                        'pembekal' => $contract->daftarSst->pembekal->nama_syarikat ?? 'N/A',
                        'bulan' => Carbon::now()->format('F Y'),
                    ]);
                    $triggered++;
                }
            }
        }

        return $triggered;
    }

    /**
     * Check if alert should be triggered (not already sent recently)
     */
    protected function shouldTriggerAlert(AlertRule $rule, $alertable, string $context): bool
    {
        // Check if already alerted in the last 24 hours for the same item
        $recentAlert = AlertLog::where('alert_rule_id', $rule->id)
            ->where('alertable_type', get_class($alertable))
            ->where('alertable_id', $alertable->id)
            ->where('triggered_at', '>=', Carbon::now()->subDay())
            ->exists();

        return !$recentAlert;
    }

    /**
     * Trigger alert and send notifications
     */
    protected function triggerAlert(AlertRule $rule, $alertable, array $data): void
    {
        DB::beginTransaction();

        try {
            // Create alert log
            $alertLog = AlertLog::create([
                'alert_rule_id' => $rule->id,
                'alertable_type' => get_class($alertable),
                'alertable_id' => $alertable->id,
                'triggered_at' => Carbon::now(),
                'trigger_data' => $data,
                'status' => 'pending',
            ]);

            // Get recipients
            $recipients = $this->getRecipients($rule, $alertable);

            // Send notifications
            $this->sendNotifications($rule, $alertLog, $recipients, $data);

            // Update alert log status
            $alertLog->update([
                'status' => 'sent',
                'sent_at' => Carbon::now(),
                'recipients_sent' => $recipients->pluck('email')->toArray(),
            ]);

            DB::commit();

            Log::info('Alert triggered successfully', [
                'rule_kod' => $rule->kod_alert,
                'alertable_type' => get_class($alertable),
                'alertable_id' => $alertable->id,
                'recipients_count' => $recipients->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($alertLog)) {
                $alertLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            Log::error('Alert trigger failed', [
                'rule_kod' => $rule->kod_alert,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get recipients based on alert rule and escalation
     */
    protected function getRecipients(AlertRule $rule, $alertable): Collection
    {
        $recipients = collect();

        // Get roles from rule
        $roles = $rule->recipient_roles ?? [];

        if (!empty($roles)) {
            $recipients = User::whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })->get();
        }

        // Add specific emails
        if (!empty($rule->recipient_emails)) {
            foreach ($rule->recipient_emails as $email) {
                if (!$recipients->contains('email', $email)) {
                    // Create temporary user object for email-only recipients
                    $recipients->push((object)[
                        'email' => $email,
                        'name' => $email,
                    ]);
                }
            }
        }

        // Filter by department if applicable
        if (method_exists($alertable, 'daftarSst') && $alertable->daftarSst) {
            $jabatanKod = $alertable->daftarSst->jabatan_kod;

            $recipients = $recipients->filter(function ($user) use ($jabatanKod) {
                return !isset($user->jabatan_kod) || $user->jabatan_kod === $jabatanKod;
            });
        }

        return $recipients;
    }

    /**
     * Send notifications via multiple channels
     */
    protected function sendNotifications(AlertRule $rule, AlertLog $alertLog, Collection $recipients, array $data): void
    {
        foreach ($recipients as $recipient) {
            try {
                // Send email
                if (isset($recipient->email)) {
                    $this->sendEmailNotification($rule, $recipient, $data);
                }

                // Send Filament notification (in-app)
                if ($recipient instanceof User) {
                    $this->sendFilamentNotification($rule, $recipient, $data);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send notification to recipient', [
                    'recipient' => $recipient->email ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification(AlertRule $rule, $recipient, array $data): void
    {
        // Replace placeholders in email subject and body
        $subject = $this->replacePlaceholders($rule->email_subject, $data);
        $body = $this->replacePlaceholders($rule->email_body, $data);

        // For now, log the email (actual sending would use Mail facade)
        Log::info('Email notification', [
            'to' => $recipient->email ?? $recipient,
            'subject' => $subject,
            'rule' => $rule->kod_alert,
        ]);

        // TODO: Implement actual email sending
        // Mail::to($recipient->email)->send(new AlertMail($subject, $body, $data));
    }

    /**
     * Send Filament notification (in-app)
     */
    protected function sendFilamentNotification(AlertRule $rule, User $recipient, array $data): void
    {
        $message = $this->replacePlaceholders($rule->notification_message, $data);

        $color = match($rule->priority) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            default => 'gray',
        };

        FilamentNotification::make()
            ->title($rule->nama_alert)
            ->body($message)
            ->icon('heroicon-o-bell-alert')
            ->iconColor($color)
            ->sendToDatabase($recipient);
    }

    /**
     * Replace placeholders in template strings
     */
    protected function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        return $template;
    }

    /**
     * Get alert statistics
     */
    public function getAlertStatistics(int $days = 30): array
    {
        $from = Carbon::now()->subDays($days);

        return [
            'total_triggered' => AlertLog::where('triggered_at', '>=', $from)->count(),
            'total_sent' => AlertLog::where('status', 'sent')->where('sent_at', '>=', $from)->count(),
            'total_failed' => AlertLog::where('status', 'failed')->where('triggered_at', '>=', $from)->count(),
            'by_rule' => AlertLog::where('triggered_at', '>=', $from)
                ->select('alert_rule_id', DB::raw('count(*) as count'))
                ->groupBy('alert_rule_id')
                ->with('alertRule:id,kod_alert,nama_alert')
                ->get(),
            'by_priority' => AlertLog::where('triggered_at', '>=', $from)
                ->join('alert_rules', 'alert_logs.alert_rule_id', '=', 'alert_rules.id')
                ->select('alert_rules.priority', DB::raw('count(*) as count'))
                ->groupBy('alert_rules.priority')
                ->get(),
        ];
    }
}
