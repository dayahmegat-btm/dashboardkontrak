<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AduanResource\Pages;
use App\Filament\Resources\AduanResource\RelationManagers;
use App\Models\Aduan;
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

class AduanResource extends Resource
{
    protected static ?string $model = Aduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Pengurusan Kontrak';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Aduan';

    protected static ?string $pluralModelLabel = 'Aduan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Aduan')
                    ->schema([
                        Forms\Components\Select::make('daftar_kontrak_id')
                            ->label('No. Kontrak')
                            ->relationship('daftarKontrak', 'no_kontrak')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih kontrak yang berkaitan'),
                        Forms\Components\TextInput::make('no_aduan')
                            ->label('No. Aduan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: ADU/2024/001'),
                        Forms\Components\DatePicker::make('tarikh_aduan')
                            ->label('Tarikh Aduan')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Butiran Aduan')
                    ->schema([
                        Forms\Components\TextInput::make('tajuk')
                            ->label('Tajuk Aduan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Tajuk ringkas aduan')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('penerangan')
                            ->label('Penerangan Aduan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Penerangan lengkap mengenai aduan'),
                        Forms\Components\Select::make('kategori')
                            ->label('Kategori Aduan')
                            ->options([
                                'kualiti_kerja' => 'Kualiti Kerja',
                                'kelewatan' => 'Kelewatan',
                                'keselamatan' => 'Keselamatan',
                                'sikap_pekerja' => 'Sikap Pekerja',
                                'tidak_patuh_spesifikasi' => 'Tidak Patuh Spesifikasi',
                                'peralatan_rosak' => 'Peralatan Rosak',
                                'kebersihan' => 'Kebersihan',
                                'lain_lain' => 'Lain-lain',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('keutamaan')
                            ->label('Keutamaan')
                            ->options([
                                'kritikal' => 'Kritikal',
                                'tinggi' => 'Tinggi',
                                'sederhana' => 'Sederhana',
                                'rendah' => 'Rendah',
                            ])
                            ->required()
                            ->default('sederhana')
                            ->helperText('Tahap keutamaan tindakan'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'baru' => 'Baru',
                                'dalam_tindakan' => 'Dalam Tindakan',
                                'menunggu_maklumbalas' => 'Menunggu Maklumbalas',
                                'selesai' => 'Selesai',
                                'ditutup' => 'Ditutup',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('baru'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Maklumat Pengadu')
                    ->schema([
                        Forms\Components\TextInput::make('pengadu_nama')
                            ->label('Nama Pengadu')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama penuh pengadu'),
                        Forms\Components\TextInput::make('pengadu_jabatan')
                            ->label('Jabatan/Unit')
                            ->maxLength(255)
                            ->placeholder('Jabatan pengadu'),
                        Forms\Components\TextInput::make('pengadu_telefon')
                            ->label('No. Telefon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('012-3456789'),
                        Forms\Components\TextInput::make('pengadu_emel')
                            ->label('E-mel')
                            ->email()
                            ->maxLength(100)
                            ->placeholder('nama@example.com'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Tindakan & Penyelesaian')
                    ->schema([
                        Forms\Components\Textarea::make('tindakan_diambil')
                            ->label('Tindakan Diambil')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Rekod tindakan yang telah/sedang diambil'),
                        Forms\Components\DatePicker::make('tarikh_tindakan')
                            ->label('Tarikh Tindakan')
                            ->displayFormat('d/m/Y')
                            ->helperText('Tarikh mula tindakan diambil'),
                        Forms\Components\DatePicker::make('tarikh_selesai')
                            ->label('Tarikh Selesai')
                            ->displayFormat('d/m/Y')
                            ->after('tarikh_aduan')
                            ->helperText('Tarikh aduan diselesaikan'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_aduan')
                    ->label('No. Aduan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->copyable(),
                Tables\Columns\TextColumn::make('daftarKontrak.no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('tarikh_aduan')
                    ->label('Tarikh')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tajuk')
                    ->label('Tajuk')
                    ->searchable()
                    ->limit(40)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->tajuk),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kualiti_kerja' => 'warning',
                        'kelewatan' => 'danger',
                        'keselamatan' => 'danger',
                        'sikap_pekerja' => 'warning',
                        'tidak_patuh_spesifikasi' => 'danger',
                        'peralatan_rosak' => 'warning',
                        'kebersihan' => 'info',
                        'lain_lain' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_')))
                    ->sortable(),
                Tables\Columns\TextColumn::make('keutamaan')
                    ->label('Keutamaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kritikal' => 'danger',
                        'tinggi' => 'warning',
                        'sederhana' => 'info',
                        'rendah' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'danger',
                        'dalam_tindakan' => 'warning',
                        'menunggu_maklumbalas' => 'info',
                        'selesai' => 'success',
                        'ditutup' => 'gray',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_')))
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengadu_nama')
                    ->label('Pengadu')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pengadu_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengadu_telefon')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tarikh_tindakan')
                    ->label('Tarikh Tindakan')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tarikh_selesai')
                    ->label('Tarikh Selesai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('daftar_kontrak_id')
                    ->label('Kontrak')
                    ->relationship('daftarKontrak', 'no_kontrak')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'kualiti_kerja' => 'Kualiti Kerja',
                        'kelewatan' => 'Kelewatan',
                        'keselamatan' => 'Keselamatan',
                        'sikap_pekerja' => 'Sikap Pekerja',
                        'tidak_patuh_spesifikasi' => 'Tidak Patuh Spesifikasi',
                        'peralatan_rosak' => 'Peralatan Rosak',
                        'kebersihan' => 'Kebersihan',
                        'lain_lain' => 'Lain-lain',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('keutamaan')
                    ->label('Keutamaan')
                    ->options([
                        'kritikal' => 'Kritikal',
                        'tinggi' => 'Tinggi',
                        'sederhana' => 'Sederhana',
                        'rendah' => 'Rendah',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'baru' => 'Baru',
                        'dalam_tindakan' => 'Dalam Tindakan',
                        'menunggu_maklumbalas' => 'Menunggu Maklumbalas',
                        'selesai' => 'Selesai',
                        'ditutup' => 'Ditutup',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('tarikh_aduan')
                    ->form([
                        Forms\Components\DatePicker::make('tarikh_dari')
                            ->label('Dari Tarikh'),
                        Forms\Components\DatePicker::make('tarikh_hingga')
                            ->label('Hingga Tarikh'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tarikh_dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tarikh_aduan', '>=', $date),
                            )
                            ->when(
                                $data['tarikh_hingga'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tarikh_aduan', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['tarikh_dari'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Dari: ' . \Carbon\Carbon::parse($data['tarikh_dari'])->format('d/m/Y'))
                                ->removeField('tarikh_dari');
                        }
                        if ($data['tarikh_hingga'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Hingga: ' . \Carbon\Carbon::parse($data['tarikh_hingga'])->format('d/m/Y'))
                                ->removeField('tarikh_hingga');
                        }
                        return $indicators;
                    }),
                Tables\Filters\Filter::make('belum_selesai')
                    ->label('Belum Selesai')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereIn('status', ['baru', 'dalam_tindakan', 'menunggu_maklumbalas'])
                    ),
                Tables\Filters\Filter::make('kritikal_tinggi')
                    ->label('Keutamaan Kritikal/Tinggi')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereIn('keutamaan', ['kritikal', 'tinggi'])
                    ),
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
                                ->withFilename(fn () => 'laporan-aduan-' . date('Y-m-d'))
                                ->withColumns([
                                    Column::make('no_aduan')->heading('No. Aduan'),
                                    Column::make('daftarKontrak.no_kontrak')->heading('No. Kontrak'),
                                    Column::make('daftarKontrak.daftarSst.no_sst')->heading('No. SST'),
                                    Column::make('daftarKontrak.daftarSst.pembekal.nama_syarikat')->heading('Nama Pembekal'),
                                    Column::make('tarikh_aduan')->heading('Tarikh Aduan')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tajuk')->heading('Tajuk'),
                                    Column::make('penerangan')->heading('Penerangan'),
                                    Column::make('kategori')->heading('Kategori')
                                        ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucwords($state, '_'))),
                                    Column::make('keutamaan')->heading('Keutamaan')
                                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                                    Column::make('status')->heading('Status')
                                        ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucwords($state, '_'))),
                                    Column::make('pengadu_nama')->heading('Nama Pengadu'),
                                    Column::make('pengadu_jabatan')->heading('Jabatan Pengadu'),
                                    Column::make('pengadu_telefon')->heading('Telefon Pengadu'),
                                    Column::make('pengadu_emel')->heading('E-mel Pengadu'),
                                    Column::make('tindakan_diambil')->heading('Tindakan Diambil'),
                                    Column::make('tarikh_tindakan')->heading('Tarikh Tindakan')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('tarikh_selesai')->heading('Tarikh Selesai')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
                                    Column::make('created_at')->heading('Tarikh Dicipta')
                                        ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
                                ])
                        ]),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tarikh_aduan', 'desc');
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
            'index' => Pages\ListAduans::route('/'),
            'create' => Pages\CreateAduan::route('/create'),
            'edit' => Pages\EditAduan::route('/{record}/edit'),
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
