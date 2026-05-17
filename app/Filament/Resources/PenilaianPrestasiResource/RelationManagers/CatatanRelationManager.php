<?php

namespace App\Filament\Resources\PenilaianPrestasiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatatanRelationManager extends RelationManager
{
    protected static string $relationship = 'catatan';

    protected static ?string $title = 'Catatan';

    protected static ?string $modelLabel = 'Catatan';

    protected static ?string $pluralModelLabel = 'Catatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Select::make('jenis_catatan')
                            ->label('Jenis Catatan')
                            ->options([
                                'penting' => 'Penting',
                                'makluman' => 'Makluman',
                                'tindakan' => 'Tindakan Diperlukan',
                                'mesyuarat' => 'Mesyuarat',
                                'komunikasi' => 'Komunikasi',
                                'peringatan' => 'Peringatan',
                                'umum' => 'Umum',
                            ])
                            ->required()
                            ->default('umum'),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->placeholder('Tulis catatan di sini...'),
                        Forms\Components\DateTimePicker::make('tarikh_catatan')
                            ->label('Tarikh & Masa')
                            ->displayFormat('d/m/Y H:i')
                            ->default(now())
                            ->required(),
                        Forms\Components\Toggle::make('is_penting')
                            ->label('Tandakan Sebagai Penting')
                            ->helperText('Catatan penting akan dipaparkan dengan jelas')
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('catatan')
            ->columns([
                Tables\Columns\IconColumn::make('is_penting')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->size('sm')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('jenis_catatan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'penting' => 'danger',
                        'tindakan' => 'warning',
                        'makluman' => 'info',
                        'peringatan' => 'warning',
                        'mesyuarat' => 'primary',
                        'komunikasi' => 'success',
                        'umum' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->searchable()
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->catatan),
                Tables\Columns\TextColumn::make('tarikh_catatan')
                    ->label('Tarikh')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_catatan')
                    ->label('Jenis Catatan')
                    ->options([
                        'penting' => 'Penting',
                        'makluman' => 'Makluman',
                        'tindakan' => 'Tindakan Diperlukan',
                        'mesyuarat' => 'Mesyuarat',
                        'komunikasi' => 'Komunikasi',
                        'peringatan' => 'Peringatan',
                        'umum' => 'Umum',
                    ]),
                Tables\Filters\TernaryFilter::make('is_penting')
                    ->label('Penting')
                    ->placeholder('Semua Catatan')
                    ->trueLabel('Catatan Penting')
                    ->falseLabel('Catatan Biasa'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Catatan'),
            ])
            ->actions([
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
            ->defaultSort('tarikh_catatan', 'desc')
            ->emptyStateHeading('Tiada Catatan')
            ->emptyStateDescription('Tambah catatan atau nota berkaitan di sini.')
            ->emptyStateIcon('heroicon-o-pencil-square');
    }
}
