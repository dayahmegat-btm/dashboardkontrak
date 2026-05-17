<?php

namespace App\Filament\Widgets;

use App\Models\DaftarKontrak;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NilaiKontrakByJabatanChart extends ChartWidget
{
    protected static ?string $heading = 'Nilai Kontrak Mengikut Jabatan (Top 10)';
    protected static ?int $sort = 6;
    protected static ?string $pollingInterval = '120s';

    protected function getData(): array
    {
        // Get active contracts value by department (top 10)
        $jabatanData = DaftarKontrak::select('daftar_sst.jabatan_id', DB::raw('sum(daftar_kontrak.nilai_kontrak) as total_nilai'))
            ->join('daftar_sst', 'daftar_kontrak.daftar_sst_id', '=', 'daftar_sst.id')
            ->whereHas('statusKontrak', function ($query) {
                $query->where('nama', 'Aktif');
            })
            ->groupBy('daftar_sst.jabatan_id')
            ->orderByDesc('total_nilai')
            ->limit(10)
            ->with('daftarSst.jabatan')
            ->get()
            ->mapWithKeys(function ($item) {
                $jabatanNama = $item->daftarSst->jabatan->nama_jabatan ?? 'Tidak Diketahui';
                // Shorten long department names
                if (strlen($jabatanNama) > 30) {
                    $jabatanNama = substr($jabatanNama, 0, 27) . '...';
                }
                return [$jabatanNama => $item->total_nilai];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Nilai Kontrak (RM)',
                    'data' => $jabatanData->values()->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(156, 163, 175, 0.8)',
                        'rgba(107, 114, 128, 0.8)',
                    ],
                ],
            ],
            'labels' => $jabatanData->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "RM " + value.toLocaleString(); }',
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
