<?php

namespace App\Filament\Resources\DaftarSstResource\Pages;

use App\Filament\Resources\DaftarSstResource;
use App\Services\SstApprovalWorkflowService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewDaftarSst extends ViewRecord
{
    protected static string $resource = DaftarSstResource::class;

    protected function getHeaderActions(): array
    {
        $workflowService = app(SstApprovalWorkflowService::class);

        return [
            Actions\Action::make('submit_approval')
                ->label('Hantar untuk Kelulusan')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Hantar SST untuk kelulusan?')
                ->modalDescription('SST ini akan dihantar kepada pihak atasan untuk kelulusan.')
                ->action(function () use ($workflowService) {
                    $result = $workflowService->submitForApproval($this->record);

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

            Actions\Action::make('approve')
                ->label('Luluskan SST')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan Kelulusan')
                        ->rows(3)
                        ->placeholder('Catatan tambahan (pilihan)'),
                ])
                ->action(function (array $data) use ($workflowService) {
                    $result = $workflowService->approve($this->record, $data['notes'] ?? null);

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

            Actions\Action::make('reject')
                ->label('Tolak SST')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Sebab Penolakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Sila nyatakan sebab penolakan...'),
                ])
                ->action(function (array $data) use ($workflowService) {
                    $result = $workflowService->reject($this->record, $data['reason']);

                    if ($result['success']) {
                        Notification::make()
                            ->success()
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

            Actions\Action::make('activate')
                ->label('Aktifkan SST')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aktifkan SST?')
                ->modalDescription('SST akan ditukar kepada status Aktif.')
                ->action(function () use ($workflowService) {
                    $result = $workflowService->activate($this->record);

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
                ->visible(fn () =>
                    $this->record->statusKontrak->kod === 'LULUS' &&
                    auth()->user()->hasAnyRole(['super-admin', 'admin', 'sk-exec'])
                ),

            Actions\Action::make('return_to_draft')
                ->label('Kembalikan ke Deraf')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Kembalikan SST ke Deraf?')
                ->modalDescription('SST akan dikembalikan ke status Deraf untuk pembetulan.')
                ->action(function () use ($workflowService) {
                    $result = $workflowService->returnToDraft($this->record);

                    if ($result['success']) {
                        Notification::make()
                            ->success()
                            ->title($result['message'])
                            ->send();
                        $this->refreshFormData(['statusKontrak', 'submitted_by', 'submitted_at']);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title($result['message'])
                            ->send();
                    }
                })
                ->visible(fn () => $this->record->statusKontrak->kod === 'TOLAK'),

            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Maklumat Kelulusan')
                    ->schema([
                        Infolists\Components\TextEntry::make('statusKontrak.nama')
                            ->label('Status Semasa')
                            ->badge()
                            ->color(fn ($record) => $record->statusKontrak->warna),
                        Infolists\Components\TextEntry::make('submittedBy.name')
                            ->label('Dihantar Oleh')
                            ->default('Belum dihantar')
                            ->visible(fn ($record) => $record->submitted_by !== null),
                        Infolists\Components\TextEntry::make('submitted_at')
                            ->label('Tarikh Hantar')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->submitted_at !== null),
                        Infolists\Components\TextEntry::make('approvedBy.name')
                            ->label('Diluluskan Oleh')
                            ->visible(fn ($record) => $record->approved_by !== null),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Tarikh Lulus')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->approved_at !== null),
                        Infolists\Components\TextEntry::make('approval_notes')
                            ->label('Catatan Kelulusan')
                            ->visible(fn ($record) => !empty($record->approval_notes)),
                        Infolists\Components\TextEntry::make('rejectedBy.name')
                            ->label('Ditolak Oleh')
                            ->visible(fn ($record) => $record->rejected_by !== null),
                        Infolists\Components\TextEntry::make('rejected_at')
                            ->label('Tarikh Tolak')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->rejected_at !== null),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Sebab Penolakan')
                            ->visible(fn ($record) => !empty($record->rejection_reason)),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) =>
                        in_array($record->statusKontrak->kod, ['HANTAR', 'SEMAK', 'LULUS', 'TOLAK', 'AKTIF'])
                    ),
            ]);
    }
}
