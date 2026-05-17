<?php

namespace App\Filament\Widgets;

use App\Models\AlertLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Amaran Terkini (7 Hari Terakhir)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AlertLog::query()
                    ->with(['alertRule'])
                    ->where('triggered_at', '>=', now()->subDays(7))
                    ->latest('triggered_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('triggered_at')
                    ->label('Tarikh & Masa')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('alertRule.kod_alert')
                    ->label('Kod Alert')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('alertRule.nama_alert')
                    ->label('Jenis Amaran')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('alertRule.priority')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'critical' => 'KRITIKAL',
                        'high' => 'TINGGI',
                        'medium' => 'SEDERHANA',
                        default => strtoupper($state),
                    }),

                Tables\Columns\TextColumn::make('alertable_type')
                    ->label('Jenis Rekod')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\\Models\\DaftarKontrak' => 'Kontrak',
                        'App\\Models\\BonPelaksanaan' => 'Bon Pelaksanaan',
                        'App\\Models\\DaftarSst' => 'SST',
                        default => class_basename($state),
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => 'Dihantar',
                        'pending' => 'Dalam Proses',
                        'failed' => 'Gagal',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Tarikh Dihantar')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->defaultSort('triggered_at', 'desc')
            ->paginated(false);
    }
}
