<?php

namespace App\Filament\Widgets;

use App\Models\DaftarKontrak;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KontrakTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Kontrak (12 Bulan Terakhir)';
    protected static ?int $sort = 5;
    protected static ?string $pollingInterval = '120s';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get last 12 months
        $months = collect();
        $newContracts = collect();
        $expiredContracts = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $months->push($date->format('M Y'));

            // Count contracts starting in this month
            $newCount = DaftarKontrak::whereDate('tarikh_mula', '>=', $startOfMonth)
                ->whereDate('tarikh_mula', '<=', $endOfMonth)
                ->count();
            $newContracts->push($newCount);

            // Count contracts expiring in this month
            $expiredCount = DaftarKontrak::whereDate('tarikh_tamat', '>=', $startOfMonth)
                ->whereDate('tarikh_tamat', '<=', $endOfMonth)
                ->count();
            $expiredContracts->push($expiredCount);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kontrak Baru',
                    'data' => $newContracts->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Kontrak Tamat',
                    'data' => $expiredContracts->toArray(),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
