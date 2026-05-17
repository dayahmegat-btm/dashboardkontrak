<?php

namespace App\Filament\Widgets;

use App\Models\DaftarKontrak;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KontrakStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Taburan Status Kontrak';
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // Get contract count by status
        $statusCounts = DaftarKontrak::select('status_kontrak_id', DB::raw('count(*) as total'))
            ->with('statusKontrak')
            ->groupBy('status_kontrak_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->statusKontrak->nama ?? 'Tidak Diketahui' => $item->total];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Bilangan Kontrak',
                    'data' => $statusCounts->values()->toArray(),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // blue
                        'rgb(34, 197, 94)',    // green
                        'rgb(251, 191, 36)',   // yellow
                        'rgb(239, 68, 68)',    // red
                        'rgb(168, 85, 247)',   // purple
                        'rgb(236, 72, 153)',   // pink
                        'rgb(20, 184, 166)',   // teal
                        'rgb(249, 115, 22)',   // orange
                    ],
                ],
            ],
            'labels' => $statusCounts->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
