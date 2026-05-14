<?php

namespace App\Services;

use App\Models\DaftarSst;
use App\Models\StatusKontrak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SstApprovalWorkflowService
{
    /**
     * Status code constants
     */
    const STATUS_DRAFT = 'DERAF';
    const STATUS_SUBMITTED = 'HANTAR';
    const STATUS_UNDER_REVIEW = 'SEMAK';
    const STATUS_APPROVED = 'LULUS';
    const STATUS_REJECTED = 'TOLAK';
    const STATUS_NEW = 'BARU';
    const STATUS_ACTIVE = 'AKTIF';

    /**
     * Submit SST for approval
     *
     * @param DaftarSst $sst
     * @param User|null $user
     * @return array
     */
    public function submitForApproval(DaftarSst $sst, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak dikenal pasti.',
            ];
        }

        // Check if SST is in draft status
        if ($sst->statusKontrak->kod !== self::STATUS_DRAFT) {
            return [
                'success' => false,
                'message' => 'Hanya SST dalam status Deraf boleh dihantar untuk kelulusan.',
            ];
        }

        // Validate SST data before submission
        $validation = $this->validateForSubmission($sst);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'SST tidak lengkap untuk dihantar.',
                'errors' => $validation['errors'],
            ];
        }

        try {
            DB::beginTransaction();

            $submittedStatus = StatusKontrak::where('kod', self::STATUS_SUBMITTED)->first();

            $sst->update([
                'status_kontrak_id' => $submittedStatus->id,
                'submitted_by' => $user->id,
                'submitted_at' => Carbon::now(),
            ]);

            DB::commit();

            Log::info('SST submitted for approval', [
                'sst_id' => $sst->id,
                'no_sst' => $sst->no_sst,
                'submitted_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'SST berjaya dihantar untuk kelulusan.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting SST for approval', [
                'sst_id' => $sst->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menghantar SST: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Mark SST as under review
     *
     * @param DaftarSst $sst
     * @param User|null $user
     * @return array
     */
    public function markAsUnderReview(DaftarSst $sst, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak dikenal pasti.',
            ];
        }

        // Check if SST is in submitted status
        if ($sst->statusKontrak->kod !== self::STATUS_SUBMITTED) {
            return [
                'success' => false,
                'message' => 'Hanya SST yang dihantar boleh ditanda sebagai dalam semakan.',
            ];
        }

        try {
            $underReviewStatus = StatusKontrak::where('kod', self::STATUS_UNDER_REVIEW)->first();

            $sst->update([
                'status_kontrak_id' => $underReviewStatus->id,
            ]);

            Log::info('SST marked as under review', [
                'sst_id' => $sst->id,
                'no_sst' => $sst->no_sst,
                'reviewed_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'SST ditanda sebagai dalam semakan.',
            ];
        } catch (\Exception $e) {
            Log::error('Error marking SST as under review', [
                'sst_id' => $sst->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Approve SST
     *
     * @param DaftarSst $sst
     * @param string|null $notes
     * @param User|null $user
     * @return array
     */
    public function approve(DaftarSst $sst, ?string $notes = null, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak dikenal pasti.',
            ];
        }

        // Check if user has permission to approve
        if (!$this->canApprove($user, $sst)) {
            return [
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk meluluskan SST ini.',
            ];
        }

        // Check if SST is in correct status for approval
        if (!in_array($sst->statusKontrak->kod, [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW])) {
            return [
                'success' => false,
                'message' => 'Hanya SST yang dihantar atau dalam semakan boleh diluluskan.',
            ];
        }

        try {
            DB::beginTransaction();

            $approvedStatus = StatusKontrak::where('kod', self::STATUS_APPROVED)->first();

            $sst->update([
                'status_kontrak_id' => $approvedStatus->id,
                'approved_by' => $user->id,
                'approved_at' => Carbon::now(),
                'approval_notes' => $notes,
                // Clear rejection fields if previously rejected
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            DB::commit();

            Log::info('SST approved', [
                'sst_id' => $sst->id,
                'no_sst' => $sst->no_sst,
                'approved_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'SST berjaya diluluskan.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving SST', [
                'sst_id' => $sst->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa meluluskan SST: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reject SST
     *
     * @param DaftarSst $sst
     * @param string $reason
     * @param User|null $user
     * @return array
     */
    public function reject(DaftarSst $sst, string $reason, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak dikenal pasti.',
            ];
        }

        // Check if user has permission to reject
        if (!$this->canReject($user, $sst)) {
            return [
                'success' => false,
                'message' => 'Anda tidak mempunyai kebenaran untuk menolak SST ini.',
            ];
        }

        // Check if SST is in correct status for rejection
        if (!in_array($sst->statusKontrak->kod, [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW])) {
            return [
                'success' => false,
                'message' => 'Hanya SST yang dihantar atau dalam semakan boleh ditolak.',
            ];
        }

        if (empty($reason)) {
            return [
                'success' => false,
                'message' => 'Sebab penolakan adalah wajib.',
            ];
        }

        try {
            DB::beginTransaction();

            $rejectedStatus = StatusKontrak::where('kod', self::STATUS_REJECTED)->first();

            $sst->update([
                'status_kontrak_id' => $rejectedStatus->id,
                'rejected_by' => $user->id,
                'rejected_at' => Carbon::now(),
                'rejection_reason' => $reason,
            ]);

            DB::commit();

            Log::info('SST rejected', [
                'sst_id' => $sst->id,
                'no_sst' => $sst->no_sst,
                'rejected_by' => $user->id,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'SST telah ditolak.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting SST', [
                'sst_id' => $sst->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menolak SST: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Return SST to draft for revision
     *
     * @param DaftarSst $sst
     * @param User|null $user
     * @return array
     */
    public function returnToDraft(DaftarSst $sst, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak dikenal pasti.',
            ];
        }

        // Can return to draft from rejected status or if user is the submitter
        if ($sst->statusKontrak->kod !== self::STATUS_REJECTED && $sst->submitted_by !== $user->id) {
            return [
                'success' => false,
                'message' => 'SST tidak boleh dikembalikan ke deraf.',
            ];
        }

        try {
            $draftStatus = StatusKontrak::where('kod', self::STATUS_DRAFT)->first();

            $sst->update([
                'status_kontrak_id' => $draftStatus->id,
                'submitted_by' => null,
                'submitted_at' => null,
            ]);

            Log::info('SST returned to draft', [
                'sst_id' => $sst->id,
                'no_sst' => $sst->no_sst,
                'returned_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'SST dikembalikan ke status Deraf.',
            ];
        } catch (\Exception $e) {
            Log::error('Error returning SST to draft', [
                'sst_id' => $sst->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Activate approved SST
     *
     * @param DaftarSst $sst
     * @param User|null $user
     * @return array
     */
    public function activate(DaftarSst $sst, ?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak dikenal pasti.',
            ];
        }

        // Check if SST is approved
        if ($sst->statusKontrak->kod !== self::STATUS_APPROVED) {
            return [
                'success' => false,
                'message' => 'Hanya SST yang diluluskan boleh diaktifkan.',
            ];
        }

        try {
            $activeStatus = StatusKontrak::where('kod', self::STATUS_ACTIVE)->first();

            $sst->update([
                'status_kontrak_id' => $activeStatus->id,
            ]);

            Log::info('SST activated', [
                'sst_id' => $sst->id,
                'no_sst' => $sst->no_sst,
                'activated_by' => $user->id,
            ]);

            return [
                'success' => true,
                'message' => 'SST berjaya diaktifkan.',
            ];
        } catch (\Exception $e) {
            Log::error('Error activating SST', [
                'sst_id' => $sst->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if user can approve SST
     *
     * @param User $user
     * @param DaftarSst $sst
     * @return bool
     */
    public function canApprove(User $user, DaftarSst $sst): bool
    {
        // Super-admin, admin, and pengarah can approve
        return $user->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec']);
    }

    /**
     * Check if user can reject SST
     *
     * @param User $user
     * @param DaftarSst $sst
     * @return bool
     */
    public function canReject(User $user, DaftarSst $sst): bool
    {
        // Super-admin, admin, and pengarah can reject
        return $user->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec']);
    }

    /**
     * Check if user can submit SST for approval
     *
     * @param User $user
     * @param DaftarSst $sst
     * @return bool
     */
    public function canSubmit(User $user, DaftarSst $sst): bool
    {
        // PIC, ketua-unit, and above can submit
        return $user->hasAnyRole(['super-admin', 'admin', 'pengarah', 'ketua-unit', 'pic', 'sk-exec']);
    }

    /**
     * Validate SST data for submission
     *
     * @param DaftarSst $sst
     * @return array
     */
    protected function validateForSubmission(DaftarSst $sst): array
    {
        $errors = [];

        // Check required fields
        if (empty($sst->no_sst)) {
            $errors[] = 'No. SST diperlukan.';
        }

        if (empty($sst->tajuk)) {
            $errors[] = 'Tajuk diperlukan.';
        }

        if (empty($sst->jabatan_id)) {
            $errors[] = 'Jabatan diperlukan.';
        }

        if (empty($sst->seksyen_unit_id)) {
            $errors[] = 'Seksyen/Unit diperlukan.';
        }

        if (empty($sst->kategori_perkhidmatan_id)) {
            $errors[] = 'Kategori Perkhidmatan diperlukan.';
        }

        if (empty($sst->kaedah_perolehan_id)) {
            $errors[] = 'Kaedah Perolehan diperlukan.';
        }

        if (empty($sst->tarikh_mula)) {
            $errors[] = 'Tarikh Mula diperlukan.';
        }

        if (empty($sst->tarikh_tamat)) {
            $errors[] = 'Tarikh Tamat diperlukan.';
        }

        if (empty($sst->tempoh_bulan)) {
            $errors[] = 'Tempoh Bulan diperlukan.';
        }

        if (empty($sst->nilai_kontrak) || $sst->nilai_kontrak <= 0) {
            $errors[] = 'Nilai Kontrak diperlukan dan mestilah lebih dari 0.';
        }

        if (empty($sst->pegawai_pengawal)) {
            $errors[] = 'Pegawai Pengawal diperlukan.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get approval status badge data
     *
     * @param DaftarSst $sst
     * @return array
     */
    public function getApprovalStatusBadge(DaftarSst $sst): array
    {
        $statusCode = $sst->statusKontrak->kod;

        return match ($statusCode) {
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
                'icon' => 'heroicon-o-eye',
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
            default => [
                'label' => $sst->statusKontrak->nama,
                'color' => $sst->statusKontrak->warna,
                'icon' => 'heroicon-o-information-circle',
            ],
        };
    }

    /**
     * Get approval history for SST
     *
     * @param DaftarSst $sst
     * @return array
     */
    public function getApprovalHistory(DaftarSst $sst): array
    {
        $history = [];

        if ($sst->submitted_at) {
            $history[] = [
                'action' => 'Dihantar untuk kelulusan',
                'user' => $sst->submittedBy?->name ?? 'Tidak diketahui',
                'timestamp' => $sst->submitted_at,
                'notes' => null,
            ];
        }

        if ($sst->approved_at) {
            $history[] = [
                'action' => 'Diluluskan',
                'user' => $sst->approvedBy?->name ?? 'Tidak diketahui',
                'timestamp' => $sst->approved_at,
                'notes' => $sst->approval_notes,
            ];
        }

        if ($sst->rejected_at) {
            $history[] = [
                'action' => 'Ditolak',
                'user' => $sst->rejectedBy?->name ?? 'Tidak diketahui',
                'timestamp' => $sst->rejected_at,
                'notes' => $sst->rejection_reason,
            ];
        }

        return $history;
    }
}
