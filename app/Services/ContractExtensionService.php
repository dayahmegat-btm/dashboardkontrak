<?php

namespace App\Services;

use App\Models\DaftarKontrak;
use App\Models\LanjutanTempoh;
use App\Models\StatusKontrak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractExtensionService
{
    // Status constants
    const STATUS_DRAFT = 'DERAF';
    const STATUS_SUBMITTED = 'HANTAR';
    const STATUS_UNDER_REVIEW = 'SEMAK';
    const STATUS_APPROVED = 'LULUS';
    const STATUS_REJECTED = 'TOLAK';
    const STATUS_ACTIVE = 'AKTIF';

    /**
     * Generate extension number
     * Format: EXT/YYYY/XXXX
     */
    public function generateExtensionNumber(?int $year = null): string
    {
        $year = $year ?? date('Y');

        $count = LanjutanTempoh::whereYear('created_at', $year)->count() + 1;

        return sprintf('EXT/%d/%04d', $year, $count);
    }

    /**
     * Get next extension sequence number for a contract
     */
    public function getNextExtensionSequence(DaftarKontrak $kontrak): int
    {
        $lastExtension = $kontrak->lanjutanTempohs()
            ->orderBy('lanjutan_ke', 'desc')
            ->first();

        return $lastExtension ? $lastExtension->lanjutan_ke + 1 : 1;
    }

    /**
     * Get the most recent extension dates for a contract
     * Returns original contract dates if no extensions exist
     */
    public function getLatestExtensionDates(DaftarKontrak $kontrak): array
    {
        $lastExtension = $kontrak->lanjutanTempohs()
            ->orderBy('lanjutan_ke', 'desc')
            ->first();

        if ($lastExtension) {
            return [
                'tarikh_mula' => $lastExtension->tarikh_mula_baru,
                'tarikh_tamat' => $lastExtension->tarikh_tamat_baru,
                'nilai_kontrak' => $lastExtension->nilai_kontrak_baru,
            ];
        }

        return [
            'tarikh_mula' => $kontrak->tarikh_mula,
            'tarikh_tamat' => $kontrak->tarikh_tamat,
            'nilai_kontrak' => $kontrak->nilai_kontrak,
        ];
    }

    /**
     * Calculate new end date from start date and extension period
     */
    public function calculateNewEndDate(Carbon $startDate, int $months): Carbon
    {
        return $startDate->copy()->addMonths($months);
    }

    /**
     * Calculate total extension period for a contract
     */
    public function getTotalExtensionPeriod(DaftarKontrak $kontrak): int
    {
        return $kontrak->lanjutanTempohs()
            ->sum('tempoh_lanjutan_bulan');
    }

    /**
     * Calculate total additional value from all extensions
     */
    public function getTotalAdditionalValue(DaftarKontrak $kontrak): float
    {
        return (float) $kontrak->lanjutanTempohs()
            ->sum('nilai_tambahan');
    }

    /**
     * Validate extension data before submission
     */
    public function validateForSubmission(LanjutanTempoh $extension): array
    {
        $errors = [];

        // Check required fields
        if (empty($extension->no_lanjutan)) {
            $errors[] = 'No. Lanjutan diperlukan.';
        }

        if (empty($extension->daftar_kontrak_id)) {
            $errors[] = 'Kontrak diperlukan.';
        }

        if (empty($extension->tarikh_mula_baru)) {
            $errors[] = 'Tarikh Mula Baru diperlukan.';
        }

        if (empty($extension->tarikh_tamat_baru)) {
            $errors[] = 'Tarikh Tamat Baru diperlukan.';
        }

        if (empty($extension->tempoh_lanjutan_bulan) || $extension->tempoh_lanjutan_bulan < 1) {
            $errors[] = 'Tempoh Lanjutan mestilah sekurang-kurangnya 1 bulan.';
        }

        if (empty($extension->sebab_lanjutan)) {
            $errors[] = 'Sebab Lanjutan diperlukan.';
        }

        // Check dates validity
        if ($extension->tarikh_mula_baru && $extension->tarikh_tamat_baru) {
            if ($extension->tarikh_tamat_baru <= $extension->tarikh_mula_baru) {
                $errors[] = 'Tarikh Tamat Baru mestilah selepas Tarikh Mula Baru.';
            }
        }

        // Check financial values
        if ($extension->nilai_kontrak_baru < $extension->nilai_kontrak_asal) {
            $errors[] = 'Nilai Kontrak Baru tidak boleh kurang daripada Nilai Kontrak Asal.';
        }

        if ($extension->nilai_tambahan < 0) {
            $errors[] = 'Nilai Tambahan tidak boleh negatif.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Submit extension for approval
     */
    public function submitForApproval(LanjutanTempoh $extension, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if ($extension->statusKontrak->kod !== self::STATUS_DRAFT) {
            return [
                'success' => false,
                'message' => 'Hanya lanjutan dalam status Deraf boleh dihantar untuk kelulusan.',
            ];
        }

        $validation = $this->validateForSubmission($extension);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'Lanjutan tidak lengkap untuk dihantar.',
                'errors' => $validation['errors'],
            ];
        }

        try {
            DB::beginTransaction();

            $submittedStatus = StatusKontrak::where('kod', self::STATUS_SUBMITTED)->first();

            $extension->update([
                'status_kontrak_id' => $submittedStatus->id,
                'submitted_by' => $user->id,
                'submitted_at' => Carbon::now(),
            ]);

            DB::commit();

            Log::info('Extension submitted for approval', [
                'extension_id' => $extension->id,
                'no_lanjutan' => $extension->no_lanjutan,
                'submitted_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'Lanjutan berjaya dihantar untuk kelulusan.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting extension', [
                'extension_id' => $extension->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menghantar lanjutan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mark extension as under review
     */
    public function markAsUnderReview(LanjutanTempoh $extension, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$this->canApprove($user, $extension)) {
            return [
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk menandakan lanjutan ini dalam semakan.',
            ];
        }

        if ($extension->statusKontrak->kod !== self::STATUS_SUBMITTED) {
            return [
                'success' => false,
                'message' => 'Hanya lanjutan yang dihantar boleh ditandakan dalam semakan.',
            ];
        }

        try {
            DB::beginTransaction();

            $reviewStatus = StatusKontrak::where('kod', self::STATUS_UNDER_REVIEW)->first();

            $extension->update([
                'status_kontrak_id' => $reviewStatus->id,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Lanjutan ditandakan dalam semakan.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Ralat semasa menandakan lanjutan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Approve extension
     */
    public function approve(LanjutanTempoh $extension, ?string $notes = null, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$this->canApprove($user, $extension)) {
            return [
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk meluluskan lanjutan ini.',
            ];
        }

        if (!in_array($extension->statusKontrak->kod, [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW])) {
            return [
                'success' => false,
                'message' => 'Hanya lanjutan yang dihantar atau dalam semakan boleh diluluskan.',
            ];
        }

        try {
            DB::beginTransaction();

            $approvedStatus = StatusKontrak::where('kod', self::STATUS_APPROVED)->first();

            $extension->update([
                'status_kontrak_id' => $approvedStatus->id,
                'approved_by' => $user->id,
                'approved_at' => Carbon::now(),
                'approval_notes' => $notes,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            DB::commit();

            Log::info('Extension approved', [
                'extension_id' => $extension->id,
                'no_lanjutan' => $extension->no_lanjutan,
                'approved_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'Lanjutan berjaya diluluskan.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving extension', [
                'extension_id' => $extension->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa meluluskan lanjutan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reject extension
     */
    public function reject(LanjutanTempoh $extension, string $reason, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$this->canReject($user, $extension)) {
            return [
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk menolak lanjutan ini.',
            ];
        }

        if (!in_array($extension->statusKontrak->kod, [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW])) {
            return [
                'success' => false,
                'message' => 'Hanya lanjutan yang dihantar atau dalam semakan boleh ditolak.',
            ];
        }

        if (empty($reason)) {
            return [
                'success' => false,
                'message' => 'Sebab penolakan diperlukan.',
            ];
        }

        try {
            DB::beginTransaction();

            $rejectedStatus = StatusKontrak::where('kod', self::STATUS_REJECTED)->first();

            $extension->update([
                'status_kontrak_id' => $rejectedStatus->id,
                'rejected_by' => $user->id,
                'rejected_at' => Carbon::now(),
                'rejection_reason' => $reason,
            ]);

            DB::commit();

            Log::info('Extension rejected', [
                'extension_id' => $extension->id,
                'no_lanjutan' => $extension->no_lanjutan,
                'rejected_by' => $user->id,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Lanjutan telah ditolak.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting extension', [
                'extension_id' => $extension->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menolak lanjutan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Return extension to draft
     */
    public function returnToDraft(LanjutanTempoh $extension, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if ($extension->statusKontrak->kod !== self::STATUS_REJECTED) {
            return [
                'success' => false,
                'message' => 'Hanya lanjutan yang ditolak boleh dikembalikan ke deraf.',
            ];
        }

        try {
            DB::beginTransaction();

            $draftStatus = StatusKontrak::where('kod', self::STATUS_DRAFT)->first();

            $extension->update([
                'status_kontrak_id' => $draftStatus->id,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Lanjutan dikembalikan ke deraf.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Ralat semasa mengembalikan lanjutan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Activate approved extension
     * This will update the parent contract's dates
     */
    public function activate(LanjutanTempoh $extension, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$this->canActivate($user, $extension)) {
            return [
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk mengaktifkan lanjutan ini.',
            ];
        }

        if ($extension->statusKontrak->kod !== self::STATUS_APPROVED) {
            return [
                'success' => false,
                'message' => 'Hanya lanjutan yang diluluskan boleh diaktifkan.',
            ];
        }

        try {
            DB::beginTransaction();

            $activeStatus = StatusKontrak::where('kod', self::STATUS_ACTIVE)->first();

            // Update extension status
            $extension->update([
                'status_kontrak_id' => $activeStatus->id,
            ]);

            // Update parent contract with new dates
            $kontrak = $extension->daftarKontrak;
            $kontrak->update([
                'tarikh_tamat' => $extension->tarikh_tamat_baru,
                'nilai_kontrak' => $extension->nilai_kontrak_baru,
                'tempoh_bulan' => Carbon::parse($kontrak->tarikh_mula)
                    ->diffInMonths(Carbon::parse($extension->tarikh_tamat_baru)),
            ]);

            DB::commit();

            Log::info('Extension activated', [
                'extension_id' => $extension->id,
                'no_lanjutan' => $extension->no_lanjutan,
                'kontrak_id' => $kontrak->id,
                'new_end_date' => $extension->tarikh_tamat_baru,
            ]);

            return [
                'success' => true,
                'message' => 'Lanjutan berjaya diaktifkan. Kontrak telah dikemaskini.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error activating extension', [
                'extension_id' => $extension->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa mengaktifkan lanjutan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if user can approve extension
     */
    public function canApprove(User $user, LanjutanTempoh $extension): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec']);
    }

    /**
     * Check if user can reject extension
     */
    public function canReject(User $user, LanjutanTempoh $extension): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec']);
    }

    /**
     * Check if user can submit extension
     */
    public function canSubmit(User $user, LanjutanTempoh $extension): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'pengarah', 'ketua-unit', 'pic', 'sk-exec']);
    }

    /**
     * Check if user can activate extension
     */
    public function canActivate(User $user, LanjutanTempoh $extension): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'sk-exec']);
    }

    /**
     * Get extension approval status badge data
     */
    public function getApprovalStatusBadge(LanjutanTempoh $extension): array
    {
        $status = $extension->statusKontrak->kod;

        $badges = [
            self::STATUS_DRAFT => [
                'label' => 'Deraf',
                'color' => 'gray',
                'icon' => 'heroicon-o-document',
            ],
            self::STATUS_SUBMITTED => [
                'label' => 'Dihantar',
                'color' => 'info',
                'icon' => 'heroicon-o-paper-airplane',
            ],
            self::STATUS_UNDER_REVIEW => [
                'label' => 'Dalam Semakan',
                'color' => 'warning',
                'icon' => 'heroicon-o-magnifying-glass',
            ],
            self::STATUS_APPROVED => [
                'label' => 'Diluluskan',
                'color' => 'success',
                'icon' => 'heroicon-o-check-circle',
            ],
            self::STATUS_REJECTED => [
                'label' => 'Ditolak',
                'color' => 'danger',
                'icon' => 'heroicon-o-x-circle',
            ],
            self::STATUS_ACTIVE => [
                'label' => 'Aktif',
                'color' => 'success',
                'icon' => 'heroicon-o-check-badge',
            ],
        ];

        return $badges[$status] ?? [
            'label' => $extension->statusKontrak->nama,
            'color' => 'gray',
            'icon' => 'heroicon-o-document',
        ];
    }

    /**
     * Get extension approval history
     */
    public function getApprovalHistory(LanjutanTempoh $extension): array
    {
        $history = [];

        if ($extension->submitted_by) {
            $history[] = [
                'action' => 'Dihantar',
                'user' => $extension->submittedBy->name,
                'date' => $extension->submitted_at,
                'notes' => null,
            ];
        }

        if ($extension->approved_by) {
            $history[] = [
                'action' => 'Diluluskan',
                'user' => $extension->approvedBy->name,
                'date' => $extension->approved_at,
                'notes' => $extension->approval_notes,
            ];
        }

        if ($extension->rejected_by) {
            $history[] = [
                'action' => 'Ditolak',
                'user' => $extension->rejectedBy->name,
                'date' => $extension->rejected_at,
                'notes' => $extension->rejection_reason,
            ];
        }

        return $history;
    }

    /**
     * Get extension summary for a contract
     */
    public function getExtensionSummary(DaftarKontrak $kontrak): array
    {
        $extensions = $kontrak->lanjutanTempohs()->orderBy('lanjutan_ke')->get();

        return [
            'total_extensions' => $extensions->count(),
            'total_months_extended' => $this->getTotalExtensionPeriod($kontrak),
            'total_additional_value' => $this->getTotalAdditionalValue($kontrak),
            'original_end_date' => $kontrak->tarikh_tamat,
            'current_end_date' => $this->getLatestExtensionDates($kontrak)['tarikh_tamat'],
            'extensions' => $extensions,
        ];
    }
}
