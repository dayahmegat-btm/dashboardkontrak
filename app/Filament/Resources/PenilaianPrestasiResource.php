<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianPrestasiResource\Pages;
use App\Filament\Resources\PenilaianPrestasiResource\RelationManagers;
use App\Models\PenilaianPrestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenilaianPrestasiResource extends Resource
{
    protected static ?string $model = PenilaianPrestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Pengurusan Kontrak';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Penilaian Prestasi';

    protected static ?string $pluralModelLabel = 'Penilaian Prestasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Penilaian')
                    ->schema([
                        Forms\Components\Select::make('daftar_kontrak_id')
                            ->label('No. Kontrak')
                            ->relationship('daftarKontrak', 'no_kontrak')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih kontrak yang akan dinilai'),
                        Forms\Components\DatePicker::make('tarikh_penilaian')
                            ->label('Tarikh Penilaian')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        Forms\Components\TextInput::make('tempoh_penilaian')
                            ->label('Tempoh Penilaian')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Januari - Jun 2024'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Kriteria Penilaian')
                    ->description('Berikan skor 0-100 untuk setiap kriteria')
                    ->schema([
                        Forms\Components\TextInput::make('skor_kualiti')
                            ->label('Skor Kualiti Kerja')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::calculateOverallScore($set, $get);
                            })
                            ->helperText('Penilaian kualiti hasil kerja'),
                        Forms\Components\TextInput::make('skor_masa')
                            ->label('Skor Ketepatan Masa')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::calculateOverallScore($set, $get);
                            })
                            ->helperText('Penilaian pematuhan jadual'),
                        Forms\Components\TextInput::make('skor_kos')
                            ->label('Skor Pengurusan Kos')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::calculateOverallScore($set, $get);
                            })
                            ->helperText('Penilaian kawalan kos'),
                        Forms\Components\TextInput::make('skor_keselamatan')
                            ->label('Skor Keselamatan & K3')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                static::calculateOverallScore($set, $get);
                            })
                            ->helperText('Penilaian aspek keselamatan'),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Keputusan Penilaian')
                    ->schema([
                        Forms\Components\TextInput::make('skor_keseluruhan')
                            ->label('Skor Keseluruhan')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->suffix('/100')
                            ->helperText('Purata skor dari semua kriteria'),
                        Forms\Components\TextInput::make('gred')
                            ->label('Gred')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('A: ≥90, B: ≥80, C: ≥70, D: ≥60, E: <60'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ulasan & Cadangan')
                    ->schema([
                        Forms\Components\Textarea::make('ulasan')
                            ->label('Ulasan Prestasi')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Ulasan terperinci mengenai prestasi kontraktor'),
                        Forms\Components\Textarea::make('cadangan_penambahbaikan')
                            ->label('Cadangan Penambahbaikan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Cadangan untuk meningkatkan prestasi'),
                    ]),

                Forms\Components\Section::make('Maklumat Penilai')
                    ->schema([
                        Forms\Components\TextInput::make('dinilai_oleh')
                            ->label('Nama Penilai')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama pegawai penilai'),
                        Forms\Components\TextInput::make('jawatan_penilai')
                            ->label('Jawatan Penilai')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Jawatan pegawai penilai'),
                        Forms\Components\FileUpload::make('fail_penilaian_path')
                            ->label('Dokumen Penilaian')
                            ->directory('penilaian-documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->helperText('Upload dokumen penilaian (PDF, max 10MB)'),
                    ])
                    ->columns(3),
            ]);
    }

    protected static function calculateOverallScore(callable $set, callable $get): void
    {
        $skorKualiti = (float) ($get('skor_kualiti') ?? 0);
        $skorMasa = (float) ($get('skor_masa') ?? 0);
        $skorKos = (float) ($get('skor_kos') ?? 0);
        $skorKeselamatan = (float) ($get('skor_keselamatan') ?? 0);

        // Calculate average
        $skorKeseluruhan = ($skorKualiti + $skorMasa + $skorKos + $skorKeselamatan) / 4;
        $set('skor_keseluruhan', round($skorKeseluruhan, 2));

        // Assign grade
        $gred = match (true) {
            $skorKeseluruhan >= 90 => 'A',
            $skorKeseluruhan >= 80 => 'B',
            $skorKeseluruhan >= 70 => 'C',
            $skorKeseluruhan >= 60 => 'D',
            default => 'E',
        };
        $set('gred', $gred);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('daftarKontrak.no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable(),
                Tables\Columns\TextColumn::make('tarikh_penilaian')
                    ->label('Tarikh Penilaian')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempoh_penilaian')
                    ->label('Tempoh')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('skor_kualiti')
                    ->label('Kualiti')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . '/100')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('skor_masa')
                    ->label('Masa')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . '/100')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('skor_kos')
                    ->label('Kos')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . '/100')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('skor_keselamatan')
                    ->label('Keselamatan')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . '/100')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('skor_keseluruhan')
                    ->label('Skor')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        $state >= 60 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . '/100'),
                Tables\Columns\TextColumn::make('gred')
                    ->label('Gred')
                    ->badge()
                    ->size('lg')
                    ->color(fn (string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        'E' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('dinilai_oleh')
                    ->label('Penilai')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jawatan_penilai')
                    ->label('Jawatan')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('fail_penilaian_path')
                    ->label('Dokumen')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('success')
                    ->falseColor('gray'),
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
                Tables\Filters\SelectFilter::make('gred')
                    ->label('Gred')
                    ->options([
                        'A' => 'A (90-100)',
                        'B' => 'B (80-89)',
                        'C' => 'C (70-79)',
                        'D' => 'D (60-69)',
                        'E' => 'E (<60)',
                    ]),
                Tables\Filters\Filter::make('skor_tinggi')
                    ->label('Skor Tinggi (≥80)')
                    ->query(fn (Builder $query): Builder => $query->where('skor_keseluruhan', '>=', 80)),
                Tables\Filters\Filter::make('skor_rendah')
                    ->label('Skor Rendah (<60)')
                    ->query(fn (Builder $query): Builder => $query->where('skor_keseluruhan', '<', 60)),
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
            ])
            ->defaultSort('tarikh_penilaian', 'desc');
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
            'index' => Pages\ListPenilaianPrestasis::route('/'),
            'create' => Pages\CreatePenilaianPrestasi::route('/create'),
            'edit' => Pages\EditPenilaianPrestasi::route('/{record}/edit'),
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
