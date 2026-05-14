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

class AduanResource extends Resource
{
    protected static ?string $model = Aduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('daftar_kontrak_id')
                    ->relationship('daftarKontrak', 'id')
                    ->required(),
                Forms\Components\TextInput::make('no_aduan')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('tarikh_aduan')
                    ->required(),
                Forms\Components\TextInput::make('tajuk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('penerangan')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('kategori')
                    ->required(),
                Forms\Components\TextInput::make('keutamaan')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('pengadu_nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pengadu_jabatan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pengadu_telefon')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('pengadu_emel')
                    ->maxLength(100),
                Forms\Components\Textarea::make('tindakan_diambil')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('tarikh_tindakan'),
                Forms\Components\DatePicker::make('tarikh_selesai'),
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
                Tables\Columns\TextColumn::make('no_aduan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarikh_aduan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tajuk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori'),
                Tables\Columns\TextColumn::make('keutamaan'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('pengadu_nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengadu_jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengadu_telefon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengadu_emel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarikh_tindakan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tarikh_selesai')
                    ->date()
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
            'index' => Pages\ListAduans::route('/'),
            'create' => Pages\CreateAduan::route('/create'),
            'edit' => Pages\EditAduan::route('/{record}/edit'),
        ];
    }
}
