<?php

namespace App\Filament\Widgets;

use App\Models\DaftarKontrak;
use App\Models\DaftarSst;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class KontrakOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        // Active contracts count
        $activeContracts = DaftarKontrak::whereHas('statusKontrak', function ($query) {
            $query->where('nama', 'Aktif');
        })->count();

        // Contracts expiring in next 90 days
        $expiringSoon = DaftarKontrak::whereHas('statusKontrak', function ($query) {
                $query->where('nama', 'Aktif');
            })
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->whereDate('tarikh_tamat', '<=', Carbon::now()->addDays(90))
            ->count();

        // Kategori 1 contracts (critical - SST issued, no PUU draft, ending ≤6 months)
        $sixMonthsFromNow = Carbon::now()->addMonths(6);
        $kategori1Count = DaftarKontrak::whereHas('daftarSst')
            ->whereNull('tarikh_deraf_ke_puu')
            ->whereDate('tarikh_tamat', '<=', $sixMonthsFromNow)
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->count();

        // Kategori 2 contracts (high risk - SST 4+ months, no PUU draft)
        $fourMonthsAgo = Carbon::now()->subMonths(4);
        $kategori2Count = DaftarKontrak::whereHas('daftarSst', function ($query) use ($fourMonthsAgo) {
                $query->whereDate('created_at', '<=', $fourMonthsAgo);
            })
            ->whereNull('tarikh_deraf_ke_puu')
            ->count();

        // Total contract value (active contracts)
        $totalValue = DaftarKontrak::whereHas('statusKontrak', function ($query) {
                $query->where('nama', 'Aktif');
            })
            ->sum('nilai_kontrak');

        // Pending approvals (submitted SSTs awaiting approval)
        $pendingApprovals = DaftarSst::whereHas('statusKontrak', function ($query) {
                $query->whereIn('nama', ['Dihantar Untuk Kelulusan', 'Dalam Semakan']);
            })
            ->count();

        return [
            Stat::make('Kontrak Aktif', $activeContracts)
                ->description('Kontrak sedang aktif')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success')
                ->chart([7, 12, 9, 14, 15, 18, $activeContracts]),

            Stat::make('Hampir Tamat', $expiringSoon)
                ->description('Akan tamat dalam 90 hari')
                ->descriptionIcon('heroicon-o-clock')
                ->color($expiringSoon > 0 ? 'warning' : 'success')
                ->chart([3, 5, 4, 6, 7, 8, $expiringSoon]),

            Stat::make('Kategori 1 (Kritikal)', $kategori1Count)
                ->description('SST ada, tiada deraf ke PUU, tamat ≤6 bulan')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($kategori1Count > 0 ? 'danger' : 'success')
                ->chart([2, 3, 2, 4, 3, 2, $kategori1Count]),

            Stat::make('Kategori 2 (Tinggi)', $kategori2Count)
                ->description('SST 4+ bulan, tiada deraf ke PUU')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color($kategori2Count > 0 ? 'danger' : 'success')
                ->chart([1, 2, 3, 2, 3, 4, $kategori2Count]),

            Stat::make('Jumlah Nilai Kontrak', 'RM ' . number_format($totalValue, 2))
                ->description('Kontrak aktif sahaja')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('Menunggu Kelulusan', $pendingApprovals)
                ->description('SST perlu diluluskan')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color($pendingApprovals > 0 ? 'info' : 'success')
                ->chart([2, 3, 1, 4, 3, 2, $pendingApprovals]),
        ];
    }
}
