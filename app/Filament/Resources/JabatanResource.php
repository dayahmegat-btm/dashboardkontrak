<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JabatanResource\Pages;
use App\Filament\Resources\JabatanResource\RelationManagers;
use App\Models\Jabatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JabatanResource extends Resource
{
    protected static ?string $model = Jabatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Jabatan';

    protected static ?string $pluralModelLabel = 'Jabatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Jabatan')
                    ->schema([
                        Forms\Components\TextInput::make('kod_jabatan')
                            ->label('Kod Jabatan')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: SUK, BPP'),
                        Forms\Components\TextInput::make('nama_jabatan')
                            ->label('Nama Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama penuh jabatan'),
                        Forms\Components\Textarea::make('penerangan')
                            ->label('Penerangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Penerangan ringkas tentang jabatan'),
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
                Tables\Columns\TextColumn::make('kod_jabatan')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('nama_jabatan')
                    ->label('Nama Jabatan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('seksyenUnits_count')
                    ->label('Bilangan Unit')
                    ->counts('seksyenUnits')
                    ->badge()
                    ->color('success'),
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
            ->defaultSort('kod_jabatan');
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
            'index' => Pages\ListJabatans::route('/'),
            'create' => Pages\CreateJabatan::route('/create'),
            'edit' => Pages\EditJabatan::route('/{record}/edit'),
        ];
    }
}
