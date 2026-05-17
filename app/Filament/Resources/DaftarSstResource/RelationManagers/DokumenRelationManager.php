<?php

namespace App\Filament\Resources\DaftarSstResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DokumenRelationManager extends RelationManager
{
    protected static string $relationship = 'dokumens';

    protected static ?string $title = 'Dokumen';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Dokumen')
                    ->schema([
                        Forms\Components\Select::make('jenis_dokumen')
                            ->label('Jenis Dokumen')
                            ->options([
                                'kontrak' => 'Dokumen Kontrak',
                                'sst' => 'Dokumen SST',
                                'bon' => 'Dokumen Bon',
                                'insurans' => 'Dokumen Insurans',
                                'lanjutan' => 'Dokumen Lanjutan',
                                'penilaian' => 'Laporan Penilaian',
                                'surat' => 'Surat Menyurat',
                                'minit_mesyuarat' => 'Minit Mesyuarat',
                                'laporan_kemajuan' => 'Laporan Kemajuan',
                                'sijil' => 'Sijil',
                                'lain_lain' => 'Lain-lain',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('nama_fail')
                            ->label('Nama Fail')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama fail dokumen'),
                        Forms\Components\FileUpload::make('path_fail')
                            ->label('Muat Naik Fail')
                            ->directory('dokumen')
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(20480)
                            ->helperText('PDF, Gambar, Word - maksimum 20MB')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catatan tambahan mengenai dokumen ini'),
                        Forms\Components\TextInput::make('no_rujukan')
                            ->label('No. Rujukan')
                            ->maxLength(100)
                            ->placeholder('No. rujukan dokumen (jika ada)'),
                        Forms\Components\DatePicker::make('tarikh_dokumen')
                            ->label('Tarikh Dokumen')
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
                Tables\Columns\TextColumn::make('jenis_dokumen')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kontrak', 'sst' => 'primary',
                        'bon', 'insurans' => 'success',
                        'penilaian', 'laporan_kemajuan' => 'info',
                        'surat', 'minit_mesyuarat' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_')))
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_fail')
                    ->label('Nama Fail')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->nama_fail),
                Tables\Columns\TextColumn::make('no_rujukan')
                    ->label('No. Rujukan')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tarikh_dokumen')
                    ->label('Tarikh')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('path_fail')
                    ->label('Fail')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dimuat Naik')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_dokumen')
                    ->label('Jenis Dokumen')
                    ->options([
                        'kontrak' => 'Dokumen Kontrak',
                        'sst' => 'Dokumen SST',
                        'bon' => 'Dokumen Bon',
                        'insurans' => 'Dokumen Insurans',
                        'lanjutan' => 'Dokumen Lanjutan',
                        'penilaian' => 'Laporan Penilaian',
                        'surat' => 'Surat Menyurat',
                        'minit_mesyuarat' => 'Minit Mesyuarat',
                        'laporan_kemajuan' => 'Laporan Kemajuan',
                        'sijil' => 'Sijil',
                        'lain_lain' => 'Lain-lain',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Dokumen'),
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
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Tiada Dokumen')
            ->emptyStateDescription('Muat naik dokumen berkaitan di sini.')
            ->emptyStateIcon('heroicon-o-document');
    }
}
