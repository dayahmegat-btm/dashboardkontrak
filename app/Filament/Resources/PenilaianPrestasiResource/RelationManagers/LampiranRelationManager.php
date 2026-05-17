<?php

namespace App\Filament\Resources\PenilaianPrestasiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LampiranRelationManager extends RelationManager
{
    protected static string $relationship = 'lampirans';

    protected static ?string $title = 'Lampiran';

    protected static ?string $modelLabel = 'Lampiran';

    protected static ?string $pluralModelLabel = 'Lampiran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Lampiran')
                    ->schema([
                        Forms\Components\Select::make('jenis_lampiran')
                            ->label('Jenis Lampiran')
                            ->options([
                                'gambar' => 'Gambar/Foto',
                                'dokumen_sokongan' => 'Dokumen Sokongan',
                                'surat' => 'Surat',
                                'laporan' => 'Laporan',
                                'invoice' => 'Invois',
                                'resit' => 'Resit',
                                'carta_organisasi' => 'Carta Organisasi',
                                'pelan_lukisan' => 'Pelan/Lukisan',
                                'sijil' => 'Sijil',
                                'lain_lain' => 'Lain-lain',
                            ])
                            ->required()
                            ->searchable()
                            ->default('lain_lain'),
                        Forms\Components\TextInput::make('nama_fail')
                            ->label('Nama Fail')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama fail lampiran'),
                        Forms\Components\FileUpload::make('path_fail')
                            ->label('Muat Naik Fail')
                            ->directory('lampiran')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/*',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/zip',
                                'application/x-rar-compressed',
                            ])
                            ->maxSize(50240)
                            ->helperText('PDF, Gambar, Word, Excel, ZIP - maksimum 50MB')
                            ->downloadable()
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Keterangan ringkas mengenai lampiran ini'),
                        Forms\Components\TextInput::make('saiz_fail')
                            ->label('Saiz Fail (KB)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Akan diisi secara automatik'),
                        Forms\Components\DatePicker::make('tarikh_muat_naik')
                            ->label('Tarikh Muat Naik')
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_fail')
            ->columns([
                Tables\Columns\TextColumn::make('jenis_lampiran')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gambar' => 'success',
                        'dokumen_sokongan' => 'primary',
                        'surat' => 'info',
                        'laporan' => 'warning',
                        'invoice', 'resit' => 'success',
                        'pelan_lukisan' => 'info',
                        'sijil' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_')))
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_fail')
                    ->label('Nama Fail')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->nama_fail),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(30)
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('path_fail')
                    ->label('Fail')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('saiz_fail')
                    ->label('Saiz')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2) . ' MB' : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tarikh_muat_naik')
                    ->label('Tarikh')
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
                Tables\Filters\SelectFilter::make('jenis_lampiran')
                    ->label('Jenis Lampiran')
                    ->options([
                        'gambar' => 'Gambar/Foto',
                        'dokumen_sokongan' => 'Dokumen Sokongan',
                        'surat' => 'Surat',
                        'laporan' => 'Laporan',
                        'invoice' => 'Invois',
                        'resit' => 'Resit',
                        'carta_organisasi' => 'Carta Organisasi',
                        'pelan_lukisan' => 'Pelan/Lukisan',
                        'sijil' => 'Sijil',
                        'lain_lain' => 'Lain-lain',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Lampiran'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Muat Turun')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->path_fail ? \Storage::url($record->path_fail) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->path_fail !== null),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Padam'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Padam Dipilih'),
                ]),
            ])
            ->defaultSort('tarikh_muat_naik', 'desc')
            ->emptyStateHeading('Tiada Lampiran')
            ->emptyStateDescription('Muat naik fail lampiran berkaitan di sini.')
            ->emptyStateIcon('heroicon-o-paper-clip');
    }
}
