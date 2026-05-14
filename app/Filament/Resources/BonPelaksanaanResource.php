<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BonPelaksanaanResource\Pages;
use App\Filament\Resources\BonPelaksanaanResource\RelationManagers;
use App\Models\BonPelaksanaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BonPelaksanaanResource extends Resource
{
    protected static ?string $model = BonPelaksanaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Pengurusan Kontrak';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Bon Pelaksanaan';

    protected static ?string $pluralModelLabel = 'Bon Pelaksanaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Bon')
                    ->schema([
                        Forms\Components\Select::make('daftar_kontrak_id')
                            ->label('No. Kontrak')
                            ->relationship('daftarKontrak', 'no_kontrak')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih kontrak yang berkaitan'),
                        Forms\Components\Select::make('jenis_bon_id')
                            ->label('Jenis Bon')
                            ->relationship('jenisBon', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('no_bon')
                            ->label('No. Bon')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->placeholder('Contoh: BON/2024/001'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Nilai & Institusi')
                    ->schema([
                        Forms\Components\TextInput::make('nilai_bon')
                            ->label('Nilai Bon (RM)')
                            ->required()
                            ->numeric()
                            ->prefix('RM')
                            ->minValue(0),
                        Forms\Components\TextInput::make('institusi_penjamin')
                            ->label('Institusi Penjamin')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama bank/institusi yang mengeluarkan bon'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tempoh Bon')
                    ->schema([
                        Forms\Components\DatePicker::make('tarikh_mula')
                            ->label('Tarikh Mula')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $tarikhTamat = $set('tarikh_tamat', null);
                                }
                            }),
                        Forms\Components\DatePicker::make('tarikh_tamat')
                            ->label('Tarikh Tamat')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->after('tarikh_mula'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Dokumen')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Bon')
                            ->options([
                                'aktif' => 'Aktif',
                                'tamat' => 'Tamat',
                                'dibatalkan' => 'Dibatalkan',
                                'diserahkan' => 'Diserahkan Balik',
                            ])
                            ->required()
                            ->default('aktif'),
                        Forms\Components\FileUpload::make('fail_bon_path')
                            ->label('Dokumen Bon')
                            ->directory('bon-documents')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->helperText('Upload dokumen bon (PDF/Gambar, max 10MB)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_bon')
                    ->label('No. Bon')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->copyable(),
                Tables\Columns\TextColumn::make('daftarKontrak.no_kontrak')
                    ->label('No. Kontrak')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('jenisBon.nama')
                    ->label('Jenis Bon')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai_bon')
                    ->label('Nilai (RM)')
                    ->money('MYR')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('institusi_penjamin')
                    ->label('Institusi')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tarikh_mula')
                    ->label('Tarikh Mula')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tarikh_tamat')
                    ->label('Tarikh Tamat')
                    ->date('d/m/Y')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'tamat' => 'warning',
                        'dibatalkan' => 'danger',
                        'diserahkan' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\IconColumn::make('fail_bon_path')
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
                Tables\Filters\SelectFilter::make('jenis_bon_id')
                    ->label('Jenis Bon')
                    ->relationship('jenisBon', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'tamat' => 'Tamat',
                        'dibatalkan' => 'Dibatalkan',
                        'diserahkan' => 'Diserahkan Balik',
                    ]),
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
            ->defaultSort('tarikh_tamat', 'asc');
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
            'index' => Pages\ListBonPelaksanaans::route('/'),
            'create' => Pages\CreateBonPelaksanaan::route('/create'),
            'edit' => Pages\EditBonPelaksanaan::route('/{record}/edit'),
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
