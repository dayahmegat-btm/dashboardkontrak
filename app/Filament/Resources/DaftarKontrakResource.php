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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('daftar_sst_id')
                    ->relationship('daftarSst', 'id')
                    ->required(),
                Forms\Components\TextInput::make('no_kontrak')
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('tarikh_kontrak')
                    ->required(),
                Forms\Components\TextInput::make('tajuk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('penerangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nilai_kontrak')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tarikh_mula')
                    ->required(),
                Forms\Components\DatePicker::make('tarikh_tamat')
                    ->required(),
                Forms\Components\TextInput::make('tempoh_bulan')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('pembekal_id')
                    ->relationship('pembekal', 'id')
                    ->required(),
                Forms\Components\TextInput::make('pegawai_pengawal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pegawai_penyelia')
                    ->maxLength(255),
                Forms\Components\Select::make('status_kontrak_id')
                    ->relationship('statusKontrak', 'id')
                    ->required(),
                Forms\Components\TextInput::make('fail_kontrak_path')
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
                Tables\Columns\TextColumn::make('daftarSst.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_kontrak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tarikh_kontrak')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tajuk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai_kontrak')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tarikh_mula')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tarikh_tamat')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempoh_bulan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembekal.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pegawai_pengawal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pegawai_penyelia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statusKontrak.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fail_kontrak_path')
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
            'index' => Pages\ListDaftarKontraks::route('/'),
            'create' => Pages\CreateDaftarKontrak::route('/create'),
            'edit' => Pages\EditDaftarKontrak::route('/{record}/edit'),
        ];
    }
}
