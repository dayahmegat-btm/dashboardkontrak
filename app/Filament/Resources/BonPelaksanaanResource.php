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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('daftar_kontrak_id')
                    ->relationship('daftarKontrak', 'id')
                    ->required(),
                Forms\Components\Select::make('jenis_bon_id')
                    ->relationship('jenisBon', 'id')
                    ->required(),
                Forms\Components\TextInput::make('no_bon')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('nilai_bon')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tarikh_mula')
                    ->required(),
                Forms\Components\DatePicker::make('tarikh_tamat')
                    ->required(),
                Forms\Components\TextInput::make('institusi_penjamin')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fail_bon_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('hari_sehingga_tamat')
                    ->numeric(),
                Forms\Components\TextInput::make('created_by')
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('daftarKontrak.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenisBon.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_bon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai_bon')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tarikh_mula')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tarikh_tamat')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('institusi_penjamin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fail_bon_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('hari_sehingga_tamat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBonPelaksanaans::route('/'),
            'create' => Pages\CreateBonPelaksanaan::route('/create'),
            'edit' => Pages\EditBonPelaksanaan::route('/{record}/edit'),
        ];
    }
}
