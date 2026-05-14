<?php

namespace App\Filament\Resources\LanjutanTempohResource\Pages;

use App\Filament\Resources\LanjutanTempohResource;
use App\Services\ContractExtensionService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLanjutanTempoh extends ViewRecord
{
    protected static string $resource = LanjutanTempohResource::class;

    protected function getHeaderActions(): array
    {
        $extensionService = app(ContractExtensionService::class);

        return [
            // Submit for approval action
            Actions\Action::make('submit_approval')
                ->label('Hantar untuk Kelulusan')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Hantar Lanjutan untuk Kelulusan?')
                ->modalDescription('Lanjutan ini akan dihantar kepada pihak atasan untuk kelulusan.')
                ->action(function () use ($extensionService) {
                    $result = $extensionService->submitForApproval($this->record);

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title($result['message'])
                            ->send();

                        $this->refreshFormData(['statusKontrak']);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title($result['message'])
                            ->body(isset($result['errors']) ? implode('<br>', $result['errors']) : '')
                            ->send();
                    }
                })
                ->visible(fn () => $this->record->statusKontrak->kod === 'DERAF'),

            // Approve action
            Actions\Action::make('approve')
                ->label('Luluskan Lanjutan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan Kelulusan')
                        ->rows(3)
                        ->placeholder('Catatan tambahan (pilihan)'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Luluskan Lanjutan?')
                ->modalDescription('Lanjutan ini akan diluluskan.')
                ->action(function (array $data) use ($extensionService) {
                    $result = $extensionService->approve($this->record, $data['notes'] ?? null);

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title($result['message'])
                            ->send();

                        $this->refreshFormData(['statusKontrak', 'approved_by', 'approved_at', 'approval_notes']);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title($result['message'])
                            ->send();
                    }
                })
                ->visible(fn () =>
                    in_array($this->record->statusKontrak->kod, ['HANTAR', 'SEMAK']) &&
                    auth()->user()->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec'])
                ),

            // Reject action
            Actions\Action::make('reject')
                ->label('Tolak Lanjutan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Sebab Penolakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Nyatakan sebab penolakan lanjutan ini...'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Tolak Lanjutan?')
                ->modalDescription('Lanjutan ini akan ditolak dan dikembalikan kepada pemohon.')
                ->action(function (array $data) use ($extensionService) {
                    $result = $extensionService->reject($this->record, $data['reason']);

                    if ($result['success']) {
                        Notification::make()
                            ->warning()
                            ->title($result['message'])
                            ->send();

                        $this->refreshFormData(['statusKontrak', 'rejected_by', 'rejected_at', 'rejection_reason']);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title($result['message'])
                            ->send();
                    }
                })
                ->visible(fn () =>
                    in_array($this->record->statusKontrak->kod, ['HANTAR', 'SEMAK']) &&
                    auth()->user()->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec'])
                ),

            // Activate action
            Actions\Action::make('activate')
                ->label('Aktifkan Lanjutan')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aktifkan Lanjutan?')
                ->modalDescription('Lanjutan ini akan diaktifkan dan kontrak asal akan dikemaskini dengan tarikh dan nilai baru.')
                ->action(function () use ($extensionService) {
                    $result = $extensionService->activate($this->record);

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title($result['message'])
                            ->duration(5000)
                            ->send();

                        $this->refreshFormData(['statusKontrak']);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title($result['message'])
                            ->send();
                    }
                })
                ->visible(fn () =>
                    $this->record->statusKontrak->kod === 'LULUS' &&
                    auth()->user()->hasAnyRole(['super-admin', 'admin', 'sk-exec'])
                ),

            // Return to draft action
            Actions\Action::make('return_to_draft')
                ->label('Kembalikan ke Deraf')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Kembalikan ke Deraf?')
                ->modalDescription('Lanjutan ini akan dikembalikan ke status deraf untuk pembetulan.')
                ->action(function () use ($extensionService) {
                    $result = $extensionService->returnToDraft($this->record);

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title($result['message'])
                            ->send();

                        $this->refreshFormData(['statusKontrak']);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title($result['message'])
                            ->send();
                    }
                })
                ->visible(fn () => $this->record->statusKontrak->kod === 'TOLAK'),

            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Maklumat Kontrak Section
                Infolists\Components\Section::make('Maklumat Kontrak')
                    ->schema([
                        Infolists\Components\TextEntry::make('daftarKontrak.no_kontrak')
                            ->label('No. Kontrak')
                            ->icon('heroicon-o-document-text')
                            ->url(fn ($record) => route('filament.admin.resources.daftar-kontraks.view', $record->daftar_kontrak_id)),

                        Infolists\Components\TextEntry::make('daftarKontrak.daftarSst.no_sst')
                            ->label('No. SST')
                            ->icon('heroicon-o-document')
                            ->url(fn ($record) => route('filament.admin.resources.daftar-ssts.view', $record->daftarKontrak->daftar_sst_id)),

                        Infolists\Components\TextEntry::make('daftarKontrak.tajuk')
                            ->label('Tajuk Kontrak')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('daftarKontrak.pembekal.nama_syarikat')
                            ->label('Pembekal'),

                        Infolists\Components\TextEntry::make('lanjutan_ke')
                            ->label('Lanjutan Ke')
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(2),

                // Maklumat Lanjutan Section
                Infolists\Components\Section::make('Maklumat Lanjutan')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('tarikh_mula_asal')
                                    ->label('Tarikh Mula Asal')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar'),

                                Infolists\Components\TextEntry::make('tarikh_tamat_asal')
                                    ->label('Tarikh Tamat Asal')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('nilai_kontrak_asal')
                                    ->label('Nilai Kontrak Asal')
                                    ->money('MYR')
                                    ->icon('heroicon-o-currency-dollar'),
                            ]),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('tarikh_mula_baru')
                                    ->label('Tarikh Mula Baru')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('tarikh_tamat_baru')
                                    ->label('Tarikh Tamat Baru')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('tempoh_lanjutan_bulan')
                                    ->label('Tempoh Lanjutan')
                                    ->suffix(' bulan')
                                    ->icon('heroicon-o-clock')
                                    ->color('info'),
                            ]),
                    ]),

                // Impak Kewangan Section
                Infolists\Components\Section::make('Impak Kewangan')
                    ->schema([
                        Infolists\Components\TextEntry::make('nilai_tambahan')
                            ->label('Nilai Tambahan')
                            ->money('MYR')
                            ->icon('heroicon-o-arrow-trending-up')
                            ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),

                        Infolists\Components\TextEntry::make('nilai_kontrak_baru')
                            ->label('Nilai Kontrak Baru (Jumlah)')
                            ->money('MYR')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success'),
                    ])
                    ->columns(2),

                // Justifikasi Section
                Infolists\Components\Section::make('Justifikasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('sebab_lanjutan')
                            ->label('Sebab Lanjutan')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('justifikasi')
                            ->label('Justifikasi Terperinci')
                            ->markdown()
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->justifikasi)),
                    ])
                    ->columns(2),

                // Maklumat Kelulusan Section
                Infolists\Components\Section::make('Maklumat Kelulusan')
                    ->schema([
                        Infolists\Components\TextEntry::make('statusKontrak.nama')
                            ->label('Status Semasa')
                            ->badge()
                            ->color(fn ($record) => $record->statusKontrak->warna)
                            ->icon(fn ($record) => app(ContractExtensionService::class)->getApprovalStatusBadge($record)['icon']),

                        Infolists\Components\TextEntry::make('submittedBy.name')
                            ->label('Dihantar Oleh')
                            ->default('Belum dihantar')
                            ->icon('heroicon-o-user')
                            ->visible(fn ($record) => $record->submitted_by !== null),

                        Infolists\Components\TextEntry::make('submitted_at')
                            ->label('Tarikh Hantar')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-calendar')
                            ->visible(fn ($record) => $record->submitted_at !== null),

                        Infolists\Components\TextEntry::make('approvedBy.name')
                            ->label('Diluluskan Oleh')
                            ->icon('heroicon-o-user')
                            ->visible(fn ($record) => $record->approved_by !== null),

                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Tarikh Lulus')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-calendar')
                            ->visible(fn ($record) => $record->approved_at !== null),

                        Infolists\Components\TextEntry::make('approval_notes')
                            ->label('Catatan Kelulusan')
                            ->markdown()
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->approval_notes)),

                        Infolists\Components\TextEntry::make('rejectedBy.name')
                            ->label('Ditolak Oleh')
                            ->icon('heroicon-o-user')
                            ->color('danger')
                            ->visible(fn ($record) => $record->rejected_by !== null),

                        Infolists\Components\TextEntry::make('rejected_at')
                            ->label('Tarikh Tolak')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-calendar')
                            ->color('danger')
                            ->visible(fn ($record) => $record->rejected_at !== null),

                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Sebab Penolakan')
                            ->markdown()
                            ->columnSpanFull()
                            ->color('danger')
                            ->visible(fn ($record) => !empty($record->rejection_reason)),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) =>
                        in_array($record->statusKontrak->kod, ['HANTAR', 'SEMAK', 'LULUS', 'TOLAK', 'AKTIF'])
                    ),

                // Dokumen Section
                Infolists\Components\Section::make('Dokumen')
                    ->schema([
                        Infolists\Components\TextEntry::make('fail_surat_lanjutan')
                            ->label('Surat Lanjutan')
                            ->icon('heroicon-o-document')
                            ->url(fn ($record) => $record->fail_surat_lanjutan ? asset('storage/' . $record->fail_surat_lanjutan) : null)
                            ->openUrlInNewTab()
                            ->default('Tiada dokumen')
                            ->color(fn ($record) => $record->fail_surat_lanjutan ? 'primary' : 'gray'),
                    ])
                    ->collapsed(),
            ]);
    }
}
