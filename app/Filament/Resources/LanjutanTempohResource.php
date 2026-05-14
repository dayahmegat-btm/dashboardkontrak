<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanjutanTempohResource\Pages;
use App\Models\LanjutanTempoh;
use App\Models\DaftarKontrak;
use App\Models\StatusKontrak;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LanjutanTempohResource extends Resource
{
    protected static ?string $model = LanjutanTempoh::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Pengurusan Kontrak';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Lanjutan Tempoh';

    protected static ?string $pluralModelLabel = 'Lanjutan Tempoh';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section 1: Maklumat Kontrak Asal
                Forms\Components\Section::make('Maklumat Kontrak Asal')
                    ->description('Maklumat kontrak yang akan dilanjutkan')
                    ->schema([
                        Forms\Components\Select::make('daftar_kontrak_id')
                            ->label('Kontrak')
                            ->relationship('daftarKontrak', 'no_kontrak')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $kontrak = DaftarKontrak::with('lanjutanTempohs')->find($state);
                                    if ($kontrak) {
                                        $lastExtension = $kontrak->lanjutanTempohs()->orderBy('lanjutan_ke', 'desc')->first();

                                        if ($lastExtension) {
                                            // If there are existing extensions, use the last extension's new dates as original
                                            $set('tarikh_mula_asal', $lastExtension->tarikh_mula_baru);
                                            $set('tarikh_tamat_asal', $lastExtension->tarikh_tamat_baru);
                                            $set('nilai_kontrak_asal', $lastExtension->nilai_kontrak_baru);
                                            $set('lanjutan_ke', $lastExtension->lanjutan_ke + 1);
                                        } else {
                                            // First extension, use kontrak's original dates
                                            $set('tarikh_mula_asal', $kontrak->tarikh_mula);
                                            $set('tarikh_tamat_asal', $kontrak->tarikh_tamat);
                                            $set('nilai_kontrak_asal', $kontrak->nilai_kontrak);
                                            $set('lanjutan_ke', 1);
                                        }

                                        // Auto-generate extension number
                                        $year = date('Y');
                                        $count = LanjutanTempoh::whereYear('created_at', $year)->count() + 1;
                                        $set('no_lanjutan', sprintf('EXT/%d/%04d', $year, $count));
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('no_lanjutan')
                            ->label('No. Lanjutan')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('EXT/2026/0001')
                            ->unique(ignoreRecord: true)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->label('Jana')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (Set $set) {
                                        $year = date('Y');
                                        $count = LanjutanTempoh::whereYear('created_at', $year)->count() + 1;
                                        $set('no_lanjutan', sprintf('EXT/%d/%04d', $year, $count));
                                    })
                            ),

                        Forms\Components\TextInput::make('lanjutan_ke')
                            ->label('Lanjutan Ke')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('tarikh_mula_asal')
                            ->label('Tarikh Mula Asal')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('tarikh_tamat_asal')
                            ->label('Tarikh Tamat Asal')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('nilai_kontrak_asal')
                            ->label('Nilai Kontrak Asal')
                            ->required()
                            ->numeric()
                            ->prefix('RM')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                // Section 2: Tempoh Lanjutan Baru
                Forms\Components\Section::make('Tempoh Lanjutan Baru')
                    ->description('Tempoh baru selepas lanjutan')
                    ->schema([
                        Forms\Components\DatePicker::make('tarikh_mula_baru')
                            ->label('Tarikh Mula Baru')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                $tempohBulan = $get('tempoh_lanjutan_bulan');
                                if ($state && $tempohBulan) {
                                    $tarikhMula = Carbon::parse($state);
                                    $tarikhTamat = $tarikhMula->copy()->addMonths($tempohBulan);
                                    $set('tarikh_tamat_baru', $tarikhTamat->format('Y-m-d'));
                                }
                            }),

                        Forms\Components\TextInput::make('tempoh_lanjutan_bulan')
                            ->label('Tempoh Lanjutan (Bulan)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                $tarikhMula = $get('tarikh_mula_baru');
                                if ($tarikhMula && $state) {
                                    $tarikhMulaParsed = Carbon::parse($tarikhMula);
                                    $tarikhTamat = $tarikhMulaParsed->copy()->addMonths((int)$state);
                                    $set('tarikh_tamat_baru', $tarikhTamat->format('Y-m-d'));
                                }
                            }),

                        Forms\Components\DatePicker::make('tarikh_tamat_baru')
                            ->label('Tarikh Tamat Baru')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(3),

                // Section 3: Justifikasi Lanjutan
                Forms\Components\Section::make('Justifikasi Lanjutan')
                    ->description('Sebab dan justifikasi lanjutan kontrak')
                    ->schema([
                        Forms\Components\Select::make('sebab_lanjutan')
                            ->label('Sebab Lanjutan')
                            ->required()
                            ->options([
                                'Kelewatan Projek' => 'Kelewatan Projek',
                                'Tambahan Skop Kerja' => 'Tambahan Skop Kerja',
                                'Perubahan Spesifikasi' => 'Perubahan Spesifikasi',
                                'Keadaan Cuaca' => 'Keadaan Cuaca',
                                'Force Majeure' => 'Force Majeure',
                                'Perubahan Polisi' => 'Perubahan Polisi',
                                'Kelulusan Lambat' => 'Kelulusan Lambat',
                                'Lain-lain' => 'Lain-lain',
                            ])
                            ->searchable(),

                        Forms\Components\Textarea::make('justifikasi')
                            ->label('Justifikasi Terperinci')
                            ->rows(4)
                            ->placeholder('Nyatakan justifikasi terperinci untuk lanjutan kontrak ini...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Section 4: Impak Kewangan
                Forms\Components\Section::make('Impak Kewangan')
                    ->description('Impak kewangan daripada lanjutan')
                    ->schema([
                        Forms\Components\TextInput::make('nilai_tambahan')
                            ->label('Nilai Tambahan')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('RM')
                            ->helperText('Masukkan 0 jika tiada nilai tambahan')
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                $nilaiAsal = $get('nilai_kontrak_asal') ?? 0;
                                $nilaiTambahan = $state ?? 0;
                                $set('nilai_kontrak_baru', $nilaiAsal + $nilaiTambahan);
                            }),

                        Forms\Components\TextInput::make('nilai_kontrak_baru')
                            ->label('Nilai Kontrak Baru (Jumlah)')
                            ->required()
                            ->numeric()
                            ->prefix('RM')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                // Section 5: Dokumen Sokongan
                Forms\Components\Section::make('Dokumen Sokongan')
                    ->description('Muat naik dokumen berkaitan')
                    ->schema([
                        Forms\Components\FileUpload::make('fail_surat_lanjutan')
                            ->label('Surat Lanjutan')
                            ->directory('lanjutan-tempoh')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->helperText('Format: PDF, Saiz maksimum: 10MB')
                            ->columnSpanFull(),
                    ]),

                // Section 6: Status
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status_kontrak_id')
                            ->label('Status')
                            ->relationship('statusKontrak', 'nama')
                            ->required()
                            ->default(function () {
                                return StatusKontrak::where('kod', 'DERAF')->first()?->id;
                            })
                            ->preload(),
                    ])
                    ->hidden(fn (?LanjutanTempoh $record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_lanjutan')
                    ->label('No. Lanjutan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-document-duplicate'),

                Tables\Columns\TextColumn::make('daftarKontrak.no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable()
                    ->url(fn (LanjutanTempoh $record) => route('filament.admin.resources.daftar-kontraks.view', $record->daftar_kontrak_id)),

                Tables\Columns\TextColumn::make('daftarKontrak.daftarSst.no_sst')
                    ->label('No. SST')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('lanjutan_ke')
                    ->label('Lanjutan Ke')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tarikh_tamat_asal')
                    ->label('Tamat Asal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tarikh_tamat_baru')
                    ->label('Tamat Baru')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn (LanjutanTempoh $record) => $record->tarikh_tamat_baru->isPast() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('tempoh_lanjutan_bulan')
                    ->label('Tempoh (Bulan)')
                    ->suffix(' bulan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nilai_tambahan')
                    ->label('Nilai Tambahan')
                    ->money('MYR')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('nilai_kontrak_baru')
                    ->label('Nilai Baru')
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('statusKontrak.nama')
                    ->label('Status')
                    ->badge()
                    ->color(fn (LanjutanTempoh $record) => $record->statusKontrak->warna ?? 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sebab_lanjutan')
                    ->label('Sebab')
                    ->wrap()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('submittedBy.name')
                    ->label('Dihantar Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Tarikh Hantar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Diluluskan Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_kontrak_id')
                    ->label('Status')
                    ->relationship('statusKontrak', 'nama')
                    ->preload(),

                Tables\Filters\SelectFilter::make('lanjutan_ke')
                    ->label('Lanjutan Ke')
                    ->options([
                        1 => 'Lanjutan 1',
                        2 => 'Lanjutan 2',
                        3 => 'Lanjutan 3',
                        4 => 'Lanjutan 4+',
                    ]),

                Tables\Filters\Filter::make('tarikh_tamat_baru')
                    ->form([
                        Forms\Components\DatePicker::make('tamat_dari')
                            ->label('Tamat Dari'),
                        Forms\Components\DatePicker::make('tamat_hingga')
                            ->label('Tamat Hingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['tamat_dari'], fn ($q) => $q->whereDate('tarikh_tamat_baru', '>=', $data['tamat_dari']))
                            ->when($data['tamat_hingga'], fn ($q) => $q->whereDate('tarikh_tamat_baru', '<=', $data['tamat_hingga']));
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
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLanjutanTempohs::route('/'),
            'create' => Pages\CreateLanjutanTempoh::route('/create'),
            'view' => Pages\ViewLanjutanTempoh::route('/{record}'),
            'edit' => Pages\EditLanjutanTempoh::route('/{record}/edit'),
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
