<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarSstResource\Pages;
use App\Filament\Resources\DaftarSstResource\RelationManagers;
use App\Models\DaftarSst;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->placeholder('Contoh: SST/BPP/2024/001'),
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
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $nilaiKomitmen = $get('nilai_komitmen') ?? 0;
                                $baki = $state - $nilaiKomitmen;
                                $set('baki_kontrak', $baki);
                            }),
                        Forms\Components\TextInput::make('nilai_komitmen')
                            ->label('Nilai Komitmen (RM)')
                            ->numeric()
                            ->prefix('RM')
                            ->default(0.00)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $nilaiKontrak = $get('nilai_kontrak') ?? 0;
                                $baki = $nilaiKontrak - $state;
                                $set('baki_kontrak', $baki);
                            }),
                        Forms\Components\TextInput::make('baki_kontrak')
                            ->label('Baki Kontrak (RM)')
                            ->numeric()
                            ->prefix('RM')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
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
                Tables\Columns\TextColumn::make('statusKontrak.nama')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_kategori_1')
                    ->label('Kat. 1')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_kategori_2')
                    ->label('Kat. 2')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
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
