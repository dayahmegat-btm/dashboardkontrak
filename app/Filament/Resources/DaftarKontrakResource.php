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
            //
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
