<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarSstResource\Pages;
use App\Filament\Resources\DaftarSstResource\RelationManagers;
use App\Models\DaftarSst;
use App\Rules\ValidContractFinancials;
use App\Rules\ValidContractPeriod;
use App\Rules\ValidSstNumber;
use App\Services\SstApprovalWorkflowService;
use App\Services\SstBusinessLogicService;
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

class DaftarSstResource extends Resource
{
    protected static ?string $model = DaftarSst::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Pengurusan Kontrak';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Daftar SST';

    protected static ?string $pluralModelLabel = 'Daftar SST';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Asas')
                    ->schema([
                        Forms\Components\TextInput::make('no_sst')
                            ->label('No. SST')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->placeholder('Contoh: SST/2026/0001')
                            ->rules([new ValidSstNumber()])
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate_sst_number')
                                    ->label('Jana No. SST')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (Forms\Set $set) {
                                        $sstService = app(SstBusinessLogicService::class);
                                        $newNumber = $sstService->generateSstNumber();
                                        $set('no_sst', $newNumber);

                                        Notification::make()
                                            ->success()
                                            ->title('No. SST dijana')
                                            ->body("No. SST baru: {$newNumber}")
                                            ->send();
                                    })
                            ),
                        Forms\Components\TextInput::make('tajuk')
                            ->label('Tajuk')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Tajuk perkhidmatan/kontrak')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('penerangan')
                            ->label('Penerangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Penerangan ringkas tentang perkhidmatan'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Maklumat Organisasi')
                    ->schema([
                        Forms\Components\Select::make('jabatan_id')
                            ->label('Jabatan')
                            ->relationship('jabatan', 'nama_jabatan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('seksyen_unit_id', null)),
                        Forms\Components\Select::make('seksyen_unit_id')
                            ->label('Seksyen/Unit')
                            ->relationship('seksyenUnit', 'nama_seksyen_unit', fn (Builder $query, callable $get) =>
                                $query->when($get('jabatan_id'), fn ($q, $jabatan) => $q->where('jabatan_id', $jabatan))
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('pembekal_id')
                            ->label('Pembekal')
                            ->relationship('pembekal', 'nama_syarikat')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih pembekal'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Kategori & Kaedah')
                    ->schema([
                        Forms\Components\Select::make('kategori_perkhidmatan_id')
                            ->label('Kategori Perkhidmatan')
                            ->relationship('kategoriPerkhidmatan', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('kaedah_perolehan_id')
                            ->label('Kaedah Perolehan')
                            ->relationship('kaedahPerolehan', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status_kontrak_id')
                            ->label('Status Kontrak')
                            ->relationship('statusKontrak', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Tempoh & Tarikh')
                    ->schema([
                        Forms\Components\DatePicker::make('tarikh_sst')
                            ->label('Tarikh SST')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->helperText('Tarikh Surat Setuju Terima (SST) dikeluarkan')
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('tarikh_mula')
                            ->label('Tarikh Mula')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $tarikhMula = $state;
                                $tempohBulan = $get('tempoh_bulan');
                                if ($tarikhMula && $tempohBulan) {
                                    $sstService = app(SstBusinessLogicService::class);
                                    $tarikhTamat = $sstService->calculateTarikhTamat($tarikhMula, (int)$tempohBulan);
                                    $set('tarikh_tamat', $tarikhTamat->format('Y-m-d'));
                                }
                            })
                            ->rules([
                                fn (callable $get) => new ValidContractPeriod(
                                    'tarikh_mula',
                                    null,
                                    $get('tarikh_tamat'),
                                    $get('tempoh_bulan')
                                )
                            ]),
                        Forms\Components\TextInput::make('tempoh_bulan')
                            ->label('Tempoh (Bulan)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $tarikhMula = $get('tarikh_mula');
                                $tempohBulan = $state;
                                if ($tarikhMula && $tempohBulan) {
                                    $sstService = app(SstBusinessLogicService::class);
                                    $tarikhTamat = $sstService->calculateTarikhTamat($tarikhMula, (int)$tempohBulan);
                                    $set('tarikh_tamat', $tarikhTamat->format('Y-m-d'));
                                }
                            })
                            ->rules([
                                fn (callable $get) => new ValidContractPeriod(
                                    'tempoh_bulan',
                                    $get('tarikh_mula'),
                                    $get('tarikh_tamat'),
                                    null
                                )
                            ]),
                        Forms\Components\DatePicker::make('tarikh_tamat')
                            ->label('Tarikh Tamat')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->rules([
                                fn (callable $get) => new ValidContractPeriod(
                                    'tarikh_tamat',
                                    $get('tarikh_mula'),
                                    null,
                                    $get('tempoh_bulan')
                                )
                            ]),
                        Forms\Components\DatePicker::make('tarikh_lanjutan_1')
                            ->label('Tarikh Lanjutan 1')
                            ->displayFormat('d/m/Y')
                            ->helperText('Opsional: Tarikh tamat selepas lanjutan pertama')
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Clear tarikh_lanjutan_2 if tarikh_lanjutan_1 is empty
                                if (empty($state) && !empty($get('tarikh_lanjutan_2'))) {
                                    $set('tarikh_lanjutan_2', null);
                                }
                            })
                            ->reactive()
                            ->rule(function (callable $get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($value && $get('tarikh_tamat')) {
                                        $tarikhTamat = \Carbon\Carbon::parse($get('tarikh_tamat'));
                                        $tarikhLanjutan1 = \Carbon\Carbon::parse($value);

                                        if ($tarikhLanjutan1 <= $tarikhTamat) {
                                            $fail('Tarikh Lanjutan 1 mestilah selepas Tarikh Tamat.');
                                        }
                                    }
                                };
                            }),
                        Forms\Components\DatePicker::make('tarikh_lanjutan_2')
                            ->label('Tarikh Lanjutan 2')
                            ->displayFormat('d/m/Y')
                            ->helperText('Opsional: Tarikh tamat selepas lanjutan kedua')
                            ->disabled(fn (callable $get) => empty($get('tarikh_lanjutan_1')))
                            ->rule(function (callable $get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($value && $get('tarikh_lanjutan_1')) {
                                        $tarikhLanjutan1 = \Carbon\Carbon::parse($get('tarikh_lanjutan_1'));
                                        $tarikhLanjutan2 = \Carbon\Carbon::parse($value);

                                        if ($tarikhLanjutan2 <= $tarikhLanjutan1) {
                                            $fail('Tarikh Lanjutan 2 mestilah selepas Tarikh Lanjutan 1.');
                                        }
                                    }
                                };
                            }),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Nilai Kewangan')
                    ->schema([
                        Forms\Components\TextInput::make('nilai_kontrak')
                            ->label('Nilai Kontrak (RM)')
                            ->required()
                            ->numeric()
                            ->prefix('RM')
                            ->minValue(0)
                            ->maxValue(100000000)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $nilaiKomitmen = $get('nilai_komitmen') ?? 0;
                                $sstService = app(SstBusinessLogicService::class);
                                $baki = $sstService->calculateBakiKontrak((float)$state, (float)$nilaiKomitmen);
                                $set('baki_kontrak', $baki);
                            })
                            ->rules([
                                fn (callable $get) => new ValidContractFinancials(
                                    'nilai_kontrak',
                                    null,
                                    $get('nilai_komitmen')
                                )
                            ]),
                        Forms\Components\TextInput::make('nilai_komitmen')
                            ->label('Nilai Komitmen (RM)')
                            ->numeric()
                            ->prefix('RM')
                            ->default(0.00)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $nilaiKontrak = $get('nilai_kontrak') ?? 0;
                                $sstService = app(SstBusinessLogicService::class);
                                $baki = $sstService->calculateBakiKontrak((float)$nilaiKontrak, (float)$state);
                                $set('baki_kontrak', $baki);
                            })
                            ->rules([
                                fn (callable $get) => new ValidContractFinancials(
                                    'nilai_komitmen',
                                    $get('nilai_kontrak'),
                                    null
                                )
                            ]),
                        Forms\Components\TextInput::make('baki_kontrak')
                            ->label('Baki Kontrak (RM)')
                            ->numeric()
                            ->prefix('RM')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated()
                            ->rules([
                                fn (callable $get) => new ValidContractFinancials(
                                    'baki_kontrak',
                                    $get('nilai_kontrak'),
                                    $get('nilai_komitmen')
                                )
                            ]),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Pegawai Berkenaan')
                    ->schema([
                        Forms\Components\TextInput::make('pegawai_pengawal')
                            ->label('Pegawai Pengawal')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama pegawai pengawal'),
                        Forms\Components\TextInput::make('pegawai_penyelia')
                            ->label('Pegawai Penyelia')
                            ->maxLength(255)
                            ->placeholder('Nama pegawai penyelia'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Penanda Kategori')
                    ->schema([
                        Forms\Components\Toggle::make('is_kategori_1')
                            ->label('Kategori 1')
                            ->helperText('Kontrak dengan nilai tinggi atau kritikal')
                            ->inline(false),
                        Forms\Components\Toggle::make('is_kategori_2')
                            ->label('Kategori 2')
                            ->helperText('Kontrak perlu perhatian khas')
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_sst')
                    ->label('No. SST')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable(),
                Tables\Columns\TextColumn::make('tajuk')
                    ->label('Tajuk')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pembekal.nama_syarikat')
                    ->label('Pembekal')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nilai_kontrak')
                    ->label('Nilai (RM)')
                    ->money('MYR')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('tarikh_sst')
                    ->label('Tarikh SST')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description('Tarikh SST dikeluarkan'),
                Tables\Columns\TextColumn::make('tarikh_mula')
                    ->label('Tarikh Mula')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tarikh_tamat')
                    ->label('Tarikh Tamat')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tarikh_lanjutan_1')
                    ->label('Lanjutan 1')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('tarikh_lanjutan_2')
                    ->label('Lanjutan 2')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('hari_sehingga_tamat')
                    ->label('Hari Lagi')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === null => 'gray',
                        $state <= 7 => 'danger',
                        $state <= 30 => 'warning',
                        $state <= 90 => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state ? $state . ' hari' : '-'),
                Tables\Columns\TextColumn::make('statusKontrak.nama')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori_risiko')
                    ->label('Kategori Risiko')
                    ->badge()
                    ->state(function ($record): ?string {
                        if ($record->is_kategori_1 && $record->is_kategori_2) {
                            return 'Kategori 1 & 2';
                        } elseif ($record->is_kategori_1) {
                            return 'Kategori 1';
                        } elseif ($record->is_kategori_2) {
                            return 'Kategori 2';
                        }
                        return null;
                    })
                    ->color(function ($record): string {
                        if ($record->is_kategori_1 || $record->is_kategori_2) {
                            return 'danger';
                        }
                        return 'gray';
                    })
                    ->icon(function ($record): ?string {
                        if ($record->is_kategori_1 || $record->is_kategori_2) {
                            return 'heroicon-o-exclamation-triangle';
                        }
                        return null;
                    })
                    ->placeholder('—')
                    ->sortable(query: function ($query, string $direction): void {
                        // Sort by highest priority first (both categories, then single categories)
                        $query->orderByRaw('(is_kategori_1 + is_kategori_2) ' . $direction);
                    })
                    ->searchable(false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jabatan_id')
                    ->label('Jabatan')
                    ->relationship('jabatan', 'nama_jabatan')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status_kontrak_id')
                    ->label('Status')
                    ->relationship('statusKontrak', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('kategori_perkhidmatan_id')
                    ->label('Kategori')
                    ->relationship('kategoriPerkhidmatan', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_kategori_1')
                    ->label('Kategori 1')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
                Tables\Filters\TernaryFilter::make('is_kategori_2')
                    ->label('Kategori 2')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
                Tables\Filters\Filter::make('high_risk')
                    ->label('Risiko Tinggi (Mana-mana Kategori)')
                    ->toggle()
                    ->query(fn ($query) => $query->where(function ($q) {
                        $q->where('is_kategori_1', true)
                          ->orWhere('is_kategori_2', true);
                    })),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('submit_approval')
                    ->label('Hantar')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Hantar SST untuk kelulusan?')
                    ->modalDescription('SST ini akan dihantar kepada pihak atasan untuk kelulusan.')
                    ->action(function (DaftarSst $record) {
                        $workflowService = app(SstApprovalWorkflowService::class);
                        $result = $workflowService->submitForApproval($record);

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title($result['message'])
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title($result['message'])
                                ->body(isset($result['errors']) ? implode('<br>', $result['errors']) : '')
                                ->send();
                        }
                    })
                    ->visible(fn (DaftarSst $record) => $record->statusKontrak->kod === 'DERAF'),
                Tables\Actions\Action::make('approve')
                    ->label('Lulus')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Kelulusan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (pilihan)'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Luluskan SST?')
                    ->modalDescription('Anda pasti untuk meluluskan SST ini?')
                    ->action(function (DaftarSst $record, array $data) {
                        $workflowService = app(SstApprovalWorkflowService::class);
                        $result = $workflowService->approve($record, $data['notes'] ?? null);

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title($result['message'])
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title($result['message'])
                                ->send();
                        }
                    })
                    ->visible(fn (DaftarSst $record) =>
                        in_array($record->statusKontrak->kod, ['HANTAR', 'SEMAK']) &&
                        auth()->user()->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec'])
                    ),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Sebab Penolakan')
                            ->required()
                            ->rows(3)
                            ->placeholder('Sila nyatakan sebab penolakan...'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Tolak SST?')
                    ->modalDescription('SST akan dikembalikan kepada pemohon untuk semakan semula.')
                    ->action(function (DaftarSst $record, array $data) {
                        $workflowService = app(SstApprovalWorkflowService::class);
                        $result = $workflowService->reject($record, $data['reason']);

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title($result['message'])
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title($result['message'])
                                ->send();
                        }
                    })
                    ->visible(fn (DaftarSst $record) =>
                        in_array($record->statusKontrak->kod, ['HANTAR', 'SEMAK']) &&
                        auth()->user()->hasAnyRole(['super-admin', 'admin', 'pengarah', 'sk-exec'])
                    ),
                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan SST?')
                    ->modalDescription('SST akan ditukar kepada status Aktif.')
                    ->action(function (DaftarSst $record) {
                        $workflowService = app(SstApprovalWorkflowService::class);
                        $result = $workflowService->activate($record);

                        if ($result['success']) {
                            Notification::make()
                                ->success()
                                ->title($result['message'])
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title($result['message'])
                                ->send();
                        }
                    })
                    ->visible(fn (DaftarSst $record) =>
                        $record->statusKontrak->kod === 'LULUS' &&
                        auth()->user()->hasAnyRole(['super-admin', 'admin', 'sk-exec'])
                    ),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'laporan-sst-' . date('Y-m-d'))
                                ->withColumns([
                                    Column::make('no_sst')->heading('No. SST'),
                                    Column::make('tajuk')->heading('Tajuk'),
                                    Column::make('jabatan.nama_jabatan')->heading('Jabatan'),
                                    Column::make('seksyenUnit.nama_unit')->heading('Seksyen/Unit'),
                                    Column::make('pembekal.nama_syarikat')->heading('Pembekal'),
                                    Column::make('nilai_kontrak')->heading('Nilai Kontrak (RM)')->formatStateUsing(fn ($state) => number_format($state, 2)),
                                    Column::make('nilai_komitmen')->heading('Nilai Komitmen (RM)')->formatStateUsing(fn ($state) => number_format($state, 2)),
                                    Column::make('baki_kontrak')->heading('Baki (RM)')->formatStateUsing(fn ($state) => number_format($state, 2)),
                                    Column::make('tarikh_sst')->heading('Tarikh SST')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_mula')->heading('Tarikh Mula')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_tamat')->heading('Tarikh Tamat')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('hari_sehingga_tamat')->heading('Hari Sehingga Tamat'),
                                    Column::make('statusKontrak.nama')->heading('Status'),
                                    Column::make('is_kategori_1')->heading('Kategori 1')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                                    Column::make('is_kategori_2')->heading('Kategori 2')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                                    Column::make('pegawai_pengawal')->heading('Pegawai Pengawal'),
                                    Column::make('pegawai_penyelia')->heading('Pegawai Penyelia'),
                                    Column::make('created_at')->heading('Tarikh Dicipta')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
                                ])
                        ]),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DokumenRelationManager::class,
            RelationManagers\CatatanRelationManager::class,
            RelationManagers\LampiranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDaftarSsts::route('/'),
            'create' => Pages\CreateDaftarSst::route('/create'),
            'view' => Pages\ViewDaftarSst::route('/{record}'),
            'edit' => Pages\EditDaftarSst::route('/{record}/edit'),
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
