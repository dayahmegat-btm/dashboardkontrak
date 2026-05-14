<?php

namespace App\Services;

use App\Models\DaftarKontrak;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractWorkflowService
{
    /**
     * Workflow stages for contract processing
     */
    const STAGE_DRAFT = 'deraf';
    const STAGE_TO_PUU = 'ke_puu';
    const STAGE_FROM_PUU = 'dari_puu';
    const STAGE_SIGNING = 'tandatangan';
    const STAGE_STAMPING = 'stamping';
    const STAGE_COMPLETED = 'siap';

    /**
     * Get workflow stages in order
     */
    public function getWorkflowStages(): array
    {
        return [
            ['key' => self::STAGE_DRAFT, 'label' => 'Deraf', 'date_field' => null],
            ['key' => self::STAGE_TO_PUU, 'label' => 'Ke PUU', 'date_field' => 'tarikh_deraf_ke_puu'],
            ['key' => self::STAGE_FROM_PUU, 'label' => 'Dari PUU', 'date_field' => 'tarikh_terima_dari_puu'],
            ['key' => self::STAGE_SIGNING, 'label' => 'Tandatangan', 'date_field' => 'tarikh_tandatangan'],
            ['key' => self::STAGE_STAMPING, 'label' => 'Stamping', 'date_field' => 'tarikh_stamping'],
            ['key' => self::STAGE_COMPLETED, 'label' => 'Siap', 'date_field' => 'is_siap'],
        ];
    }

    /**
     * Get current workflow stage for a contract
     */
    public function getCurrentStage(DaftarKontrak $kontrak): string
    {
        if ($kontrak->is_siap || $kontrak->tarikh_stamping) {
            return self::STAGE_COMPLETED;
        }

        if ($kontrak->tarikh_tandatangan) {
            return self::STAGE_STAMPING;
        }

        if ($kontrak->tarikh_terima_dari_puu) {
            return self::STAGE_SIGNING;
        }

        if ($kontrak->tarikh_deraf_ke_puu) {
            return self::STAGE_FROM_PUU;
        }

        return self::STAGE_DRAFT;
    }

    /**
     * Get workflow progress percentage
     */
    public function getWorkflowProgress(DaftarKontrak $kontrak): int
    {
        $totalStages = 6;
        $completedStages = 1; // Always at least at draft stage

        if ($kontrak->tarikh_deraf_ke_puu) {
            $completedStages++;
        }
        if ($kontrak->tarikh_terima_dari_puu) {
            $completedStages++;
        }
        if ($kontrak->tarikh_tandatangan) {
            $completedStages++;
        }
        if ($kontrak->tarikh_stamping) {
            $completedStages++;
        }
        if ($kontrak->is_siap) {
            $completedStages++;
        }

        return (int) (($completedStages / $totalStages) * 100);
    }

    /**
     * Calculate days in each workflow stage
     */
    public function getStageTimeline(DaftarKontrak $kontrak): array
    {
        $timeline = [];

        // Draft stage
        $draftStart = $kontrak->created_at;
        $draftEnd = $kontrak->tarikh_deraf_ke_puu ?? Carbon::now();
        $timeline['deraf'] = [
            'start' => $draftStart,
            'end' => $draftEnd,
            'days' => $draftStart->diffInDays($draftEnd),
        ];

        // To PUU stage
        if ($kontrak->tarikh_deraf_ke_puu) {
            $puuEnd = $kontrak->tarikh_terima_dari_puu ?? Carbon::now();
            $timeline['ke_puu'] = [
                'start' => $kontrak->tarikh_deraf_ke_puu,
                'end' => $puuEnd,
                'days' => Carbon::parse($kontrak->tarikh_deraf_ke_puu)->diffInDays($puuEnd),
            ];
        }

        // From PUU to signing stage
        if ($kontrak->tarikh_terima_dari_puu) {
            $signingEnd = $kontrak->tarikh_tandatangan ?? Carbon::now();
            $timeline['dari_puu'] = [
                'start' => $kontrak->tarikh_terima_dari_puu,
                'end' => $signingEnd,
                'days' => Carbon::parse($kontrak->tarikh_terima_dari_puu)->diffInDays($signingEnd),
            ];
        }

        // Signing to stamping stage
        if ($kontrak->tarikh_tandatangan) {
            $stampingEnd = $kontrak->tarikh_stamping ?? Carbon::now();
            $timeline['tandatangan'] = [
                'start' => $kontrak->tarikh_tandatangan,
                'end' => $stampingEnd,
                'days' => Carbon::parse($kontrak->tarikh_tandatangan)->diffInDays($stampingEnd),
            ];
        }

        return $timeline;
    }

    /**
     * Mark contract as sent to PUU
     */
    public function markAsSentToPUU(DaftarKontrak $kontrak, ?Carbon $date = null): array
    {
        try {
            $date = $date ?? Carbon::now();

            DB::beginTransaction();

            $kontrak->update([
                'tarikh_deraf_ke_puu' => $date,
            ]);

            $this->logWorkflowTransition($kontrak, 'Sent to PUU', $date);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Kontrak berjaya ditandakan sebagai dihantar ke PUU.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark contract as sent to PUU', [
                'contract_id' => $kontrak->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menandakan kontrak: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Mark contract as received from PUU
     */
    public function markAsReceivedFromPUU(DaftarKontrak $kontrak, ?Carbon $date = null): array
    {
        try {
            $date = $date ?? Carbon::now();

            if (! $kontrak->tarikh_deraf_ke_puu) {
                return [
                    'success' => false,
                    'message' => 'Kontrak mesti dihantar ke PUU terlebih dahulu.',
                ];
            }

            if ($date->lt(Carbon::parse($kontrak->tarikh_deraf_ke_puu))) {
                return [
                    'success' => false,
                    'message' => 'Tarikh terima dari PUU tidak boleh lebih awal dari tarikh hantar ke PUU.',
                ];
            }

            DB::beginTransaction();

            $kontrak->update([
                'tarikh_terima_dari_puu' => $date,
            ]);

            $this->logWorkflowTransition($kontrak, 'Received from PUU', $date);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Kontrak berjaya ditandakan sebagai diterima dari PUU.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark contract as received from PUU', [
                'contract_id' => $kontrak->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menandakan kontrak: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Mark contract as signed
     */
    public function markAsSigned(DaftarKontrak $kontrak, ?Carbon $date = null): array
    {
        try {
            $date = $date ?? Carbon::now();

            if (! $kontrak->tarikh_terima_dari_puu) {
                return [
                    'success' => false,
                    'message' => 'Kontrak mesti diterima dari PUU terlebih dahulu.',
                ];
            }

            if ($date->lt(Carbon::parse($kontrak->tarikh_terima_dari_puu))) {
                return [
                    'success' => false,
                    'message' => 'Tarikh tandatangan tidak boleh lebih awal dari tarikh terima dari PUU.',
                ];
            }

            DB::beginTransaction();

            $kontrak->update([
                'tarikh_tandatangan' => $date,
            ]);

            $this->logWorkflowTransition($kontrak, 'Contract signed', $date);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Kontrak berjaya ditandakan sebagai ditandatangani.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark contract as signed', [
                'contract_id' => $kontrak->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menandakan kontrak: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Mark contract as stamped and complete
     */
    public function markAsStamped(DaftarKontrak $kontrak, ?Carbon $date = null): array
    {
        try {
            $date = $date ?? Carbon::now();

            if (! $kontrak->tarikh_tandatangan) {
                return [
                    'success' => false,
                    'message' => 'Kontrak mesti ditandatangani terlebih dahulu.',
                ];
            }

            if ($date->lt(Carbon::parse($kontrak->tarikh_tandatangan))) {
                return [
                    'success' => false,
                    'message' => 'Tarikh stamping tidak boleh lebih awal dari tarikh tandatangan.',
                ];
            }

            DB::beginTransaction();

            $kontrak->update([
                'tarikh_stamping' => $date,
                'is_siap' => true, // Auto-mark as completed
            ]);

            $this->logWorkflowTransition($kontrak, 'Contract stamped and completed', $date);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Kontrak berjaya ditandakan sebagai di-stamp dan siap.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark contract as stamped', [
                'contract_id' => $kontrak->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa menandakan kontrak: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Reset workflow to draft stage
     */
    public function resetWorkflow(DaftarKontrak $kontrak): array
    {
        try {
            DB::beginTransaction();

            $kontrak->update([
                'tarikh_deraf_ke_puu' => null,
                'tarikh_terima_dari_puu' => null,
                'tarikh_tandatangan' => null,
                'tarikh_stamping' => null,
                'is_siap' => false,
            ]);

            $this->logWorkflowTransition($kontrak, 'Workflow reset to draft', Carbon::now());

            DB::commit();

            return [
                'success' => true,
                'message' => 'Workflow kontrak berjaya dikembalikan ke peringkat deraf.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset contract workflow', [
                'contract_id' => $kontrak->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Ralat semasa mereset workflow: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Log workflow transition
     */
    protected function logWorkflowTransition(DaftarKontrak $kontrak, string $action, Carbon $date): void
    {
        Log::info('Contract workflow transition', [
            'contract_id' => $kontrak->id,
            'contract_no' => $kontrak->no_kontrak,
            'action' => $action,
            'date' => $date->toDateString(),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Get workflow status badge configuration
     */
    public function getWorkflowStatusBadge(DaftarKontrak $kontrak): array
    {
        $stage = $this->getCurrentStage($kontrak);
        $progress = $this->getWorkflowProgress($kontrak);

        $badges = [
            self::STAGE_DRAFT => ['color' => 'gray', 'icon' => 'heroicon-o-document', 'label' => 'Deraf'],
            self::STAGE_TO_PUU => ['color' => 'info', 'icon' => 'heroicon-o-arrow-right-circle', 'label' => 'Ke PUU'],
            self::STAGE_FROM_PUU => ['color' => 'warning', 'icon' => 'heroicon-o-arrow-left-circle', 'label' => 'Dari PUU'],
            self::STAGE_SIGNING => ['color' => 'primary', 'icon' => 'heroicon-o-pencil-square', 'label' => 'Tandatangan'],
            self::STAGE_STAMPING => ['color' => 'secondary', 'icon' => 'heroicon-o-document-check', 'label' => 'Stamping'],
            self::STAGE_COMPLETED => ['color' => 'success', 'icon' => 'heroicon-o-check-circle', 'label' => 'Siap'],
        ];

        return array_merge($badges[$stage], ['progress' => $progress]);
    }

    /**
     * Validate workflow dates
     */
    public function validateWorkflowDates(DaftarKontrak $kontrak): array
    {
        $errors = [];

        if ($kontrak->tarikh_terima_dari_puu && ! $kontrak->tarikh_deraf_ke_puu) {
            $errors[] = 'Tarikh terima dari PUU memerlukan tarikh hantar ke PUU.';
        }

        if ($kontrak->tarikh_tandatangan && ! $kontrak->tarikh_terima_dari_puu) {
            $errors[] = 'Tarikh tandatangan memerlukan tarikh terima dari PUU.';
        }

        if ($kontrak->tarikh_stamping && ! $kontrak->tarikh_tandatangan) {
            $errors[] = 'Tarikh stamping memerlukan tarikh tandatangan.';
        }

        // Check date sequence
        $dates = [
            'tarikh_deraf_ke_puu' => $kontrak->tarikh_deraf_ke_puu,
            'tarikh_terima_dari_puu' => $kontrak->tarikh_terima_dari_puu,
            'tarikh_tandatangan' => $kontrak->tarikh_tandatangan,
            'tarikh_stamping' => $kontrak->tarikh_stamping,
        ];

        $previousDate = null;
        foreach ($dates as $field => $date) {
            if ($date && $previousDate) {
                $current = Carbon::parse($date);
                $previous = Carbon::parse($previousDate);

                if ($current->lt($previous)) {
                    $errors[] = 'Tarikh workflow tidak mengikut urutan yang betul.';
                    break;
                }
            }

            if ($date) {
                $previousDate = $date;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
