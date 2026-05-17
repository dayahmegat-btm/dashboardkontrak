<?php

namespace App\Filament\Widgets;

use App\Models\BonPelaksanaan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BonPelaksanaanOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        // Active performance bonds
        $activeBonds = BonPelaksanaan::where('status', 'aktif')->count();

        // Bonds expiring in 7 days (critical)
        $expiringCritical = BonPelaksanaan::where('status', 'aktif')
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->whereDate('tarikh_tamat', '<=', Carbon::now()->addDays(7))
            ->count();

        // Bonds expiring in 30 days (warning)
        $expiringWarning = BonPelaksanaan::where('status', 'aktif')
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->whereDate('tarikh_tamat', '<=', Carbon::now()->addDays(30))
            ->count();

        // Bonds expiring in 90 days (notice)
        $expiringNotice = BonPelaksanaan::where('status', 'aktif')
            ->whereDate('tarikh_tamat', '>=', Carbon::now())
            ->whereDate('tarikh_tamat', '<=', Carbon::now()->addDays(90))
            ->count();

        // Total bond value (active)
        $totalBondValue = BonPelaksanaan::where('status', 'aktif')
            ->sum('nilai_bon');

        // Unreturned bonds (completed contracts)
        $unreturnedBonds = BonPelaksanaan::whereHas('daftarKontrak', function ($query) {
                $query->where('is_siap', true)
                    ->whereNotNull('tarikh_stamping');
            })
            ->where('status', 'aktif')
            ->count();

        return [
            Stat::make('Bon Aktif', $activeBonds)
                ->description('Bon pelaksanaan sedang aktif')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('success')
                ->chart([12, 15, 14, 16, 18, 19, $activeBonds]),

            Stat::make('Tamat dalam 7 Hari', $expiringCritical)
                ->description('Kritikal - Tindakan segera')
                ->descriptionIcon('heroicon-o-bell-alert')
                ->color($expiringCritical > 0 ? 'danger' : 'success')
                ->chart([0, 1, 0, 2, 1, 0, $expiringCritical]),

            Stat::make('Tamat dalam 30 Hari', $expiringWarning)
                ->description('Amaran - Perlu tindakan')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($expiringWarning > 0 ? 'warning' : 'success')
                ->chart([2, 3, 2, 4, 3, 2, $expiringWarning]),

            Stat::make('Tamat dalam 90 Hari', $expiringNotice)
                ->description('Makluman - Pantau')
                ->descriptionIcon('heroicon-o-clock')
                ->color($expiringNotice > 0 ? 'info' : 'success')
                ->chart([4, 5, 6, 5, 7, 8, $expiringNotice]),

            Stat::make('Jumlah Nilai Bon', 'RM ' . number_format($totalBondValue, 2))
                ->description('Bon aktif sahaja')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Bon Belum Dipulangkan', $unreturnedBonds)
                ->description('Kontrak siap, bon masih aktif')
                ->descriptionIcon('heroicon-o-arrow-uturn-left')
                ->color($unreturnedBonds > 0 ? 'warning' : 'success')
                ->chart([1, 2, 1, 3, 2, 1, $unreturnedBonds]),
        ];
    }
}
