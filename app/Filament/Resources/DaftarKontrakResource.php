<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarKontrakResource\Pages;
use App\Filament\Resources\DaftarKontrakResource\RelationManagers;
use App\Models\DaftarKontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class DaftarKontrakResource extends Resource
{
    protected static ?string $model = DaftarKontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'Pengurusan Kontrak';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Daftar Kontrak';

    protected static ?string $pluralModelLabel = 'Daftar Kontrak';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Kontrak')
                    ->schema([
                        Forms\Components\Select::make('daftar_sst_id')
                            ->label('No. SST')
                            ->relationship('daftarSst', 'no_sst')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih SST yang berkaitan'),
                        Forms\Components\TextInput::make('no_kontrak')
                            ->label('No. Kontrak')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->placeholder('Contoh: KONTRAK/BPP/2024/001'),
                        Forms\Components\DatePicker::make('tarikh_kontrak')
                            ->label('Tarikh Kontrak')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        Forms\Components\TextInput::make('tajuk')
                            ->label('Tajuk Kontrak')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Tajuk kontrak formal')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('penerangan')
                            ->label('Penerangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Penerangan ringkas tentang kontrak'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Tempoh Kontrak')
                    ->schema([
                        Forms\Components\DatePicker::make('tarikh_mula')
                            ->label('Tarikh Mula')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $tarikhMula = $state;
                                $tempohBulan = $get('tempoh_bulan');
                                if ($tarikhMula && $tempohBulan) {
                                    $tarikhTamat = date('Y-m-d', strtotime("+{$tempohBulan} months", strtotime($tarikhMula)));
                                    $set('tarikh_tamat', $tarikhTamat);
                                }
                            }),
                        Forms\Components\TextInput::make('tempoh_bulan')
                            ->label('Tempoh (Bulan)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $tarikhMula = $get('tarikh_mula');
                                $tempohBulan = $state;
                                if ($tarikhMula && $tempohBulan) {
                                    $tarikhTamat = date('Y-m-d', strtotime("+{$tempohBulan} months", strtotime($tarikhMula)));
                                    $set('tarikh_tamat', $tarikhTamat);
                                }
                            }),
                        Forms\Components\DatePicker::make('tarikh_tamat')
                            ->label('Tarikh Tamat')
                            ->required()
                            ->displayFormat('d/m/Y'),
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

                Forms\Components\Section::make('Pembekal & Nilai')
                    ->schema([
                        Forms\Components\Select::make('pembekal_id')
                            ->label('Pembekal')
                            ->relationship('pembekal', 'nama_syarikat')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_syarikat')
                                    ->label('Nama Syarikat')
                                    ->required(),
                                Forms\Components\TextInput::make('no_pendaftaran')
                                    ->label('No. Pendaftaran')
                                    ->required(),
                            ]),
                        Forms\Components\TextInput::make('nilai_kontrak')
                            ->label('Nilai Kontrak (RM)')
                            ->required()
                            ->numeric()
                            ->prefix('RM')
                            ->minValue(0),
                    ])
                    ->columns(2),

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

                Forms\Components\Section::make('Penjejakan Workflow')
                    ->description('Penjejakan peringkat pemprosesan kontrak: Deraf → PUU → Tandatangan → Stamping → Siap')
                    ->schema([
                        Forms\Components\DatePicker::make('tarikh_deraf_ke_puu')
                            ->label('Tarikh Ke PUU')
                            ->displayFormat('d/m/Y')
                            ->helperText('Tarikh kontrak dihantar ke Pejabat Undang-Undang'),
                        Forms\Components\DatePicker::make('tarikh_terima_dari_puu')
                            ->label('Tarikh Dari PUU')
                            ->displayFormat('d/m/Y')
                            ->helperText('Tarikh kontrak diterima kembali dari PUU')
                            ->rule(function (callable $get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($value && $get('tarikh_deraf_ke_puu')) {
                                        $toPuu = \Carbon\Carbon::parse($get('tarikh_deraf_ke_puu'));
                                        $fromPuu = \Carbon\Carbon::parse($value);
                                        if ($fromPuu->lt($toPuu)) {
                                            $fail('Tarikh dari PUU mestilah selepas tarikh ke PUU.');
                                        }
                                    }
                                };
                            }),
                        Forms\Components\DatePicker::make('tarikh_tandatangan')
                            ->label('Tarikh Tandatangan')
                            ->displayFormat('d/m/Y')
                            ->helperText('Tarikh kontrak ditandatangani')
                            ->rule(function (callable $get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($value && $get('tarikh_terima_dari_puu')) {
                                        $fromPuu = \Carbon\Carbon::parse($get('tarikh_terima_dari_puu'));
                                        $signed = \Carbon\Carbon::parse($value);
                                        if ($signed->lt($fromPuu)) {
                                            $fail('Tarikh tandatangan mestilah selepas tarikh dari PUU.');
                                        }
                                    }
                                };
                            }),
                        Forms\Components\DatePicker::make('tarikh_stamping')
                            ->label('Tarikh Stamping')
                            ->displayFormat('d/m/Y')
                            ->helperText('Tarikh kontrak di-stamp (auto-mark sebagai siap)')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Auto-set is_siap when stamping date is filled
                                if ($state) {
                                    $set('is_siap', true);
                                } else {
                                    $set('is_siap', false);
                                }
                            })
                            ->rule(function (callable $get) {
                                return function ($attribute, $value, $fail) use ($get) {
                                    if ($value && $get('tarikh_tandatangan')) {
                                        $signed = \Carbon\Carbon::parse($get('tarikh_tandatangan'));
                                        $stamped = \Carbon\Carbon::parse($value);
                                        if ($stamped->lt($signed)) {
                                            $fail('Tarikh stamping mestilah selepas tarikh tandatangan.');
                                        }
                                    }
                                };
                            }),
                        Forms\Components\Toggle::make('is_siap')
                            ->label('Kontrak Siap')
                            ->helperText('Tandakan jika kontrak telah selesai diproses')
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->collapsible(),

                Forms\Components\Section::make('Catatan Dalaman')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_dalaman')
                            ->label('Catatan')
                            ->rows(4)
                            ->placeholder('Catatan dalaman berkaitan workflow atau status kontrak')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Status & Dokumen')
                    ->schema([
                        Forms\Components\Select::make('status_kontrak_id')
                            ->label('Status Kontrak')
                            ->relationship('statusKontrak', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\FileUpload::make('fail_kontrak_path')
                            ->label('Dokumen Kontrak')
                            ->directory('kontrak-documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->helperText('Upload dokumen kontrak (PDF, max 10MB)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->copyable(),
                Tables\Columns\TextColumn::make('daftarSst.no_sst')
                    ->label('No. SST')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('tajuk')
                    ->label('Tajuk')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('pembekal.nama_syarikat')
                    ->label('Pembekal')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('nilai_kontrak')
                    ->label('Nilai (RM)')
                    ->money('MYR')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('tarikh_kontrak')
                    ->label('Tarikh Kontrak')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statusKontrak.nama')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('fail_kontrak_path')
                    ->label('Dokumen')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('workflow_stage')
                    ->label('Peringkat Workflow')
                    ->badge()
                    ->state(function (DaftarKontrak $record): string {
                        $service = app(\App\Services\ContractWorkflowService::class);
                        $badge = $service->getWorkflowStatusBadge($record);
                        return $badge['label'];
                    })
                    ->color(function (DaftarKontrak $record): string {
                        $service = app(\App\Services\ContractWorkflowService::class);
                        $badge = $service->getWorkflowStatusBadge($record);
                        return $badge['color'];
                    })
                    ->icon(function (DaftarKontrak $record): string {
                        $service = app(\App\Services\ContractWorkflowService::class);
                        $badge = $service->getWorkflowStatusBadge($record);
                        return $badge['icon'];
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(query: function ($query, string $direction): void {
                        // Sort by completion status
                        $query->orderBy('is_siap', $direction === 'asc' ? 'asc' : 'desc');
                    }),
                Tables\Columns\IconColumn::make('is_siap')
                    ->label('Siap')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tarikh_stamping')
                    ->label('Tarikh Stamping')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('daftar_sst_id')
                    ->label('SST')
                    ->relationship('daftarSst', 'no_sst')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('pembekal_id')
                    ->label('Pembekal')
                    ->relationship('pembekal', 'nama_syarikat')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status_kontrak_id')
                    ->label('Status')
                    ->relationship('statusKontrak', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_siap')
                    ->label('Status Siap')
                    ->placeholder('Semua kontrak')
                    ->trueLabel('Siap sahaja')
                    ->falseLabel('Dalam pemprosesan')
                    ->queries(
                        true: fn ($query) => $query->where('is_siap', true),
                        false: fn ($query) => $query->where('is_siap', false),
                        blank: fn ($query) => $query,
                    ),
                Tables\Filters\Filter::make('tarikh_stamping')
                    ->label('Belum Stamping')
                    ->toggle()
                    ->query(fn ($query) => $query->whereNull('tarikh_stamping')->where('is_siap', false)),
                Tables\Filters\Filter::make('tarikh_tamat')
                    ->form([
                        Forms\Components\DatePicker::make('tarikh_tamat_dari')
                            ->label('Dari Tarikh'),
                        Forms\Components\DatePicker::make('tarikh_tamat_hingga')
                            ->label('Hingga Tarikh'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tarikh_tamat_dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tarikh_tamat', '>=', $date),
                            )
                            ->when(
                                $data['tarikh_tamat_hingga'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tarikh_tamat', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['tarikh_tamat_dari'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Tamat dari: ' . \Carbon\Carbon::parse($data['tarikh_tamat_dari'])->format('d/m/Y'))
                                ->removeField('tarikh_tamat_dari');
                        }

                        if ($data['tarikh_tamat_hingga'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Tamat hingga: ' . \Carbon\Carbon::parse($data['tarikh_tamat_hingga'])->format('d/m/Y'))
                                ->removeField('tarikh_tamat_hingga');
                        }

                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('workflow_stage')
                    ->label('Peringkat Workflow')
                    ->options([
                        'draft' => 'Deraf',
                        'to_puu' => 'Dihantar ke PUU',
                        'from_puu' => 'Diterima dari PUU',
                        'signed' => 'Ditandatangani',
                        'stamped' => 'Di-stamp',
                        'completed' => 'Siap',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! isset($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'draft' => $query->whereNull('tarikh_deraf_ke_puu'),
                            'to_puu' => $query->whereNotNull('tarikh_deraf_ke_puu')->whereNull('tarikh_terima_dari_puu'),
                            'from_puu' => $query->whereNotNull('tarikh_terima_dari_puu')->whereNull('tarikh_tandatangan'),
                            'signed' => $query->whereNotNull('tarikh_tandatangan')->whereNull('tarikh_stamping'),
                            'stamped' => $query->whereNotNull('tarikh_stamping')->where('is_siap', false),
                            'completed' => $query->where('is_siap', true),
                            default => $query,
                        };
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'laporan-kontrak-' . date('Y-m-d'))
                                ->withColumns([
                                    Column::make('no_kontrak')->heading('No. Kontrak'),
                                    Column::make('daftarSst.no_sst')->heading('No. SST'),
                                    Column::make('tajuk')->heading('Tajuk Kontrak'),
                                    Column::make('pembekal.nama_syarikat')->heading('Pembekal'),
                                    Column::make('nilai_kontrak')->heading('Nilai Kontrak (RM)')->formatStateUsing(fn ($state) => number_format($state, 2)),
                                    Column::make('tarikh_kontrak')->heading('Tarikh Kontrak')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_mula')->heading('Tarikh Mula')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_tamat')->heading('Tarikh Tamat')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_deraf_ke_puu')->heading('Tarikh Ke PUU')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_terima_dari_puu')->heading('Tarikh Dari PUU')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_tandatangan')->heading('Tarikh Tandatangan')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_stamping')->heading('Tarikh Stamping')->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('is_siap')->heading('Status Siap')->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak'),
                                    Column::make('statusKontrak.nama')->heading('Status'),
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
            'index' => Pages\ListDaftarKontraks::route('/'),
            'create' => Pages\CreateDaftarKontrak::route('/create'),
            'edit' => Pages\EditDaftarKontrak::route('/{record}/edit'),
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
