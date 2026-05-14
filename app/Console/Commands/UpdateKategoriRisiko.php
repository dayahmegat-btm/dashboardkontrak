<?php

namespace App\Console\Commands;

use App\Models\DaftarSst;
use App\Models\StatusKontrak;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateKategoriRisiko extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kategori:update {--dry-run : Run without saving changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Kategori 1 & 2 risk flags for SST contracts based on defined criteria';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no changes will be saved');
        }

        $this->info('Starting Kategori Risiko calculation...');

        try {
            DB::beginTransaction();

            // Get active status IDs
            $activeStatuses = StatusKontrak::whereIn('kod', ['AKTIF', 'BARU', 'LULUS'])->pluck('id')->toArray();

            if (empty($activeStatuses)) {
                $this->error('No active status found. Please ensure AKTIF status exists.');
                return self::FAILURE;
            }

            // First, reset all kategori flags
            if (! $dryRun) {
                DaftarSst::query()->update([
                    'is_kategori_1' => false,
                    'is_kategori_2' => false,
                ]);
                $this->info('Reset all kategori flags');
            }

            // Calculate Kategori 1 & 2
            $kategori1Count = $this->calculateKategori1($activeStatuses, $dryRun);
            $kategori2Count = $this->calculateKategori2($activeStatuses, $dryRun);

            if (! $dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            // Display summary
            $this->newLine();
            $this->info('╔══════════════════════════════════════╗');
            $this->info('║     Kategori Risiko Summary          ║');
            $this->info('╠══════════════════════════════════════╣');
            $this->info(sprintf('║ Kategori 1 (Expiring Soon):  %6d ║', $kategori1Count));
            $this->info(sprintf('║ Kategori 2 (Long Processing): %6d ║', $kategori2Count));
            $this->info('╚══════════════════════════════════════╝');

            if ($dryRun) {
                $this->newLine();
                $this->warn('DRY RUN complete - no changes were saved');
            } else {
                $this->newLine();
                $this->info('✓ Kategori calculation completed successfully');

                // Log the execution
                Log::info('Kategori risk calculation completed', [
                    'kategori_1_count' => $kategori1Count,
                    'kategori_2_count' => $kategori2Count,
                    'timestamp' => Carbon::now(),
                ]);
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('Failed to update kategori: '.$e->getMessage());
            Log::error('Kategori calculation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Calculate Kategori 1: Contracts expiring soon (within 180 days) without PUU submission
     * Criteria:
     * - SST exists and is active
     * - Days until tarikh_tamat <= 180
     * - Related contract has NOT been sent to PUU (tarikh_deraf_ke_puu IS NULL)
     * - Status is active
     */
    protected function calculateKategori1(array $activeStatuses, bool $dryRun): int
    {
        $this->info('Calculating Kategori 1 (Expiring Soon)...');

        $today = Carbon::now();
        $expiryThreshold = $today->copy()->addDays(180);

        // Query for SST records expiring within 180 days
        $sstRecords = DaftarSst::query()
            ->whereIn('status_kontrak_id', $activeStatuses)
            ->where('tarikh_tamat', '<=', $expiryThreshold)
            ->where('tarikh_tamat', '>=', $today)
            ->with(['daftarKontraks' => function ($query) {
                $query->whereNull('tarikh_deraf_ke_puu');
            }])
            ->get();

        $count = 0;

        foreach ($sstRecords as $sst) {
            // Check if any related contract has not been sent to PUU
            $hasUnsentContract = $sst->daftarKontraks->where('tarikh_deraf_ke_puu', null)->isNotEmpty();

            if ($hasUnsentContract) {
                $daysUntilExpiry = $today->diffInDays(Carbon::parse($sst->tarikh_tamat), false);

                if (! $dryRun) {
                    $sst->update(['is_kategori_1' => true]);
                }

                $this->line(sprintf(
                    '  → SST: %s | Days until expiry: %d | Status: Kategori 1',
                    $sst->no_sst,
                    $daysUntilExpiry
                ));

                $count++;
            }
        }

        $this->info("Found {$count} SST records in Kategori 1");

        return $count;
    }

    /**
     * Calculate Kategori 2: Long processing time (>= 120 days) without PUU submission
     * Criteria:
     * - SST exists and is active
     * - Days since created_at >= 120
     * - Related contract has NOT been sent to PUU (tarikh_deraf_ke_puu IS NULL)
     * - Status is active
     */
    protected function calculateKategori2(array $activeStatuses, bool $dryRun): int
    {
        $this->info('Calculating Kategori 2 (Long Processing)...');

        $today = Carbon::now();
        $processingThreshold = $today->copy()->subDays(120);

        // Query for SST records created more than 120 days ago
        $sstRecords = DaftarSst::query()
            ->whereIn('status_kontrak_id', $activeStatuses)
            ->where('created_at', '<=', $processingThreshold)
            ->with(['daftarKontraks' => function ($query) {
                $query->whereNull('tarikh_deraf_ke_puu');
            }])
            ->get();

        $count = 0;

        foreach ($sstRecords as $sst) {
            // Check if any related contract has not been sent to PUU
            $hasUnsentContract = $sst->daftarKontraks->where('tarikh_deraf_ke_puu', null)->isNotEmpty();

            if ($hasUnsentContract) {
                $daysSinceCreated = Carbon::parse($sst->created_at)->diffInDays($today);

                if (! $dryRun) {
                    $sst->update(['is_kategori_2' => true]);
                }

                $this->line(sprintf(
                    '  → SST: %s | Days since created: %d | Status: Kategori 2',
                    $sst->no_sst,
                    $daysSinceCreated
                ));

                $count++;
            }
        }

        $this->info("Found {$count} SST records in Kategori 2");

        return $count;
    }
}
