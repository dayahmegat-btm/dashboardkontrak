<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeksyenUnitResource\Pages;
use App\Filament\Resources\SeksyenUnitResource\RelationManagers;
use App\Models\SeksyenUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeksyenUnitResource extends Resource
{
    protected static ?string $model = SeksyenUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Seksyen/Unit';

    protected static ?string $pluralModelLabel = 'Seksyen/Unit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Seksyen/Unit')
                    ->schema([
                        Forms\Components\Select::make('jabatan_id')
                            ->label('Jabatan')
                            ->relationship('jabatan', 'nama_jabatan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih jabatan'),
                        Forms\Components\TextInput::make('kod_seksyen_unit')
                            ->label('Kod Seksyen/Unit')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: BPP-PER'),
                        Forms\Components\TextInput::make('nama_seksyen_unit')
                            ->label('Nama Seksyen/Unit')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama penuh seksyen/unit'),
                        Forms\Components\Textarea::make('penerangan')
                            ->label('Penerangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Penerangan ringkas tentang seksyen/unit'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('kod_seksyen_unit')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('nama_seksyen_unit')
                    ->label('Nama Seksyen/Unit')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dikemaskini')
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif sahaja')
                    ->falseLabel('Tidak aktif sahaja'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kod_seksyen_unit');
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
            'index' => Pages\ListSeksyenUnits::route('/'),
            'create' => Pages\CreateSeksyenUnit::route('/create'),
            'edit' => Pages\EditSeksyenUnit::route('/{record}/edit'),
        ];
    }
}
