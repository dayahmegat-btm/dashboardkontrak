<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembekalResource\Pages;
use App\Filament\Resources\PembekalResource\RelationManagers;
use App\Models\Pembekal;
use App\Services\IDaftarService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class PembekalResource extends Resource
{
    protected static ?string $model = Pembekal::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Pembekal';

    protected static ?string $pluralModelLabel = 'Pembekal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Syarikat')
                    ->schema([
                        Forms\Components\TextInput::make('nama_syarikat')
                            ->label('Nama Syarikat')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama penuh syarikat'),
                        Forms\Components\TextInput::make('no_pendaftaran')
                            ->label('No. Pendaftaran SSM')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: 202001234567')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('lookup_idaftar')
                                    ->label('Cari iDaftar')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->action(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if (empty($state)) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Sila masukkan no. pendaftaran')
                                                ->send();
                                            return;
                                        }

                                        $idaftarService = app(IDaftarService::class);

                                        // Validate format first
                                        if (!$idaftarService->isValidRegistrationFormat($state)) {
                                            Notification::make()
                                                ->danger()
                                                ->title('Format no. pendaftaran tidak sah')
                                                ->body('No. pendaftaran mesti mengandungi 5-20 aksara alphanumerik.')
                                                ->send();
                                            return;
                                        }

                                        $supplierData = $idaftarService->getSupplierData($state);

                                        if ($supplierData === null) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Pembekal tidak dijumpai')
                                                ->body('Tiada data pembekal dijumpai dalam iDaftar untuk no. pendaftaran ini.')
                                                ->send();
                                            return;
                                        }

                                        // Check if supplier is active
                                        if (($supplierData['status'] ?? '') !== 'Aktif') {
                                            Notification::make()
                                                ->warning()
                                                ->title('Pembekal tidak aktif')
                                                ->body('Pembekal ini berstatus: ' . ($supplierData['status'] ?? 'Tidak Diketahui'))
                                                ->send();
                                        }

                                        // Populate form fields
                                        $set('nama_syarikat', $supplierData['nama_syarikat']);
                                        $set('alamat', $supplierData['alamat']);
                                        $set('no_telefon', $supplierData['no_telefon']);
                                        $set('emel', $supplierData['emel']);
                                        $set('pic_nama', $supplierData['pic_nama']);
                                        $set('pic_telefon', $supplierData['pic_telefon']);
                                        $set('pic_emel', $supplierData['pic_emel']);

                                        Notification::make()
                                            ->success()
                                            ->title('Data berjaya diisi')
                                            ->body('Data pembekal dari iDaftar telah diisi ke dalam borang.')
                                            ->send();
                                    })
                            ),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Alamat lengkap syarikat'),
                        Forms\Components\TextInput::make('no_telefon')
                            ->label('No. Telefon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 04-1234567'),
                        Forms\Components\TextInput::make('emel')
                            ->label('Emel')
                            ->email()
                            ->maxLength(100)
                            ->placeholder('emel@syarikat.com'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Maklumat Person In Charge (PIC)')
                    ->schema([
                        Forms\Components\TextInput::make('pic_nama')
                            ->label('Nama PIC')
                            ->maxLength(255)
                            ->placeholder('Nama wakil syarikat'),
                        Forms\Components\TextInput::make('pic_telefon')
                            ->label('No. Telefon PIC')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 012-3456789'),
                        Forms\Components\TextInput::make('pic_emel')
                            ->label('Emel PIC')
                            ->email()
                            ->maxLength(100)
                            ->placeholder('pic@syarikat.com')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_syarikat')
                    ->label('Nama Syarikat')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('no_pendaftaran')
                    ->label('No. Pendaftaran')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('no_telefon')
                    ->label('Telefon')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('emel')
                    ->label('Emel')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pic_nama')
                    ->label('PIC')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('daftar_ssts_count')
                    ->label('Bil. SST')
                    ->counts('daftarSsts')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dipadam')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif sahaja')
                    ->falseLabel('Tidak aktif sahaja'),

                Tables\Filters\Filter::make('bilangan_sst')
                    ->form([
                        Forms\Components\TextInput::make('sst_min')
                            ->label('Min. Bilangan SST')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('sst_max')
                            ->label('Max. Bilangan SST')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['sst_min'] !== null,
                                fn (Builder $query): Builder => $query->has('daftarSsts', '>=', (int)$data['sst_min']),
                            )
                            ->when(
                                $data['sst_max'] !== null,
                                fn (Builder $query): Builder => $query->has('daftarSsts', '<=', (int)$data['sst_max']),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (($data['sst_min'] ?? null) !== null) {
                            $indicators[] = Tables\Filters\Indicator::make('Min. SST: ' . $data['sst_min'])
                                ->removeField('sst_min');
                        }
                        if (($data['sst_max'] ?? null) !== null) {
                            $indicators[] = Tables\Filters\Indicator::make('Max. SST: ' . $data['sst_max'])
                                ->removeField('sst_max');
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('has_pic')
                    ->label('Maklumat PIC')
                    ->placeholder('Semua Pembekal')
                    ->trueLabel('Ada Maklumat PIC')
                    ->falseLabel('Tiada Maklumat PIC')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('pic_nama')->where('pic_nama', '!=', ''),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->whereNull('pic_nama')->orWhere('pic_nama', '=', '');
                        }),
                    ),

                Tables\Filters\TernaryFilter::make('has_contact')
                    ->label('Maklumat Hubungan')
                    ->placeholder('Semua Pembekal')
                    ->trueLabel('Ada No. Telefon/Emel')
                    ->falseLabel('Tiada No. Telefon & Emel')
                    ->queries(
                        true: fn (Builder $query) => $query->where(function ($q) {
                            $q->whereNotNull('no_telefon')->where('no_telefon', '!=', '')
                                ->orWhereNotNull('emel')->where('emel', '!=', '');
                        }),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->where(function ($subQ) {
                                $subQ->whereNull('no_telefon')->orWhere('no_telefon', '=', '');
                            })->where(function ($subQ) {
                                $subQ->whereNull('emel')->orWhere('emel', '=', '');
                            });
                        }),
                    ),

                Tables\Filters\TernaryFilter::make('has_sst')
                    ->label('Status SST')
                    ->placeholder('Semua Pembekal')
                    ->trueLabel('Ada SST Aktif')
                    ->falseLabel('Tiada SST')
                    ->queries(
                        true: fn (Builder $query) => $query->has('daftarSsts', '>', 0),
                        false: fn (Builder $query) => $query->doesntHave('daftarSsts'),
                    ),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('refresh_idaftar')
                    ->label('Kemaskini dari iDaftar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Pembekal $record) {
                        $idaftarService = app(IDaftarService::class);

                        // Clear cache first
                        $idaftarService->clearCache($record->no_pendaftaran);

                        // Get fresh data
                        $supplierData = $idaftarService->getSupplierData($record->no_pendaftaran);

                        if ($supplierData === null) {
                            Notification::make()
                                ->warning()
                                ->title('Pembekal tidak dijumpai')
                                ->body('Tiada data pembekal dijumpai dalam iDaftar.')
                                ->send();
                            return;
                        }

                        // Update record
                        $record->update([
                            'nama_syarikat' => $supplierData['nama_syarikat'] ?? $record->nama_syarikat,
                            'alamat' => $supplierData['alamat'] ?? $record->alamat,
                            'no_telefon' => $supplierData['no_telefon'] ?? $record->no_telefon,
                            'emel' => $supplierData['emel'] ?? $record->emel,
                            'pic_nama' => $supplierData['pic_nama'] ?? $record->pic_nama,
                            'pic_telefon' => $supplierData['pic_telefon'] ?? $record->pic_telefon,
                            'pic_emel' => $supplierData['pic_emel'] ?? $record->pic_emel,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Data berjaya dikemaskini')
                            ->body('Data pembekal telah dikemaskini dari iDaftar.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Kemaskini data dari iDaftar?')
                    ->modalDescription('Tindakan ini akan menggantikan data semasa dengan data terkini dari iDaftar.')
                    ->modalSubmitActionLabel('Ya, kemaskini'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'laporan-pembekal-' . date('Y-m-d'))
                                ->withColumns([
                                    Column::make('nama_syarikat')->heading('Nama Syarikat'),
                                    Column::make('no_pendaftaran')->heading('No. Pendaftaran SSM'),
                                    Column::make('alamat')->heading('Alamat'),
                                    Column::make('no_telefon')->heading('No. Telefon'),
                                    Column::make('emel')->heading('Emel'),
                                    Column::make('pic_nama')->heading('Nama PIC'),
                                    Column::make('pic_telefon')->heading('Telefon PIC'),
                                    Column::make('pic_emel')->heading('Emel PIC'),
                                    Column::make('daftar_ssts_count')->heading('Bilangan SST'),
                                    Column::make('is_active')->heading('Status Aktif')
                                        ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Tidak Aktif'),
                                    Column::make('created_at')->heading('Tarikh Dicipta')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
                                    Column::make('updated_at')->heading('Tarikh Kemaskini')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
                                    Column::make('deleted_at')->heading('Tarikh Dipadam')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
                                ])
                        ]),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_syarikat');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembekals::route('/'),
            'create' => Pages\CreatePembekal::route('/create'),
            'edit' => Pages\EditPembekal::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
