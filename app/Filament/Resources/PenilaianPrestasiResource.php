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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('daftar_kontrak_id')
                    ->relationship('daftarKontrak', 'id')
                    ->required(),
                Forms\Components\DatePicker::make('tarikh_penilaian')
                    ->required(),
                Forms\Components\TextInput::make('tempoh_penilaian')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('skor_kualiti')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('skor_masa')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('skor_kos')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('skor_keselamatan')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('skor_keseluruhan')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('ulasan')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cadangan_penambahbaikan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('gred')
                    ->required(),
                Forms\Components\TextInput::make('fail_penilaian_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('dinilai_oleh')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jawatan_penilai')
                    ->required()
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('tarikh_penilaian')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempoh_penilaian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('skor_kualiti')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skor_masa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skor_kos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skor_keselamatan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skor_keseluruhan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gred'),
                Tables\Columns\TextColumn::make('fail_penilaian_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dinilai_oleh')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jawatan_penilai')
                    ->searchable(),
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
            'index' => Pages\ListPenilaianPrestasis::route('/'),
            'create' => Pages\CreatePenilaianPrestasi::route('/create'),
            'edit' => Pages\EditPenilaianPrestasi::route('/{record}/edit'),
        ];
    }
}
