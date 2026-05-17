<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class CheckDailyAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and trigger daily alerts for contracts, bonds, and performance evaluations';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alertService)
    {
        $this->info('🔔 Starting daily alert check...');
        $this->info('Time: ' . now()->format('d/m/Y H:i:s'));
        $this->newLine();

        try {
            $results = $alertService->checkAndTriggerAlerts();

            $this->info('✅ Alert check completed successfully!');
            $this->newLine();

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Rules Checked', $results['checked']],
                    ['Alerts Triggered', $results['triggered']],
                    ['Notifications Sent', $results['sent']],
                    ['Failures', $results['failed']],
                ]
            );

            if (!empty($results['errors'])) {
                $this->newLine();
                $this->warn('⚠️  Some errors occurred:');
                foreach ($results['errors'] as $error) {
                    $this->error('  • ' . $error);
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Alert check failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
