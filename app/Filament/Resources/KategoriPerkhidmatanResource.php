<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriPerkhidmatanResource\Pages;
use App\Models\KategoriPerkhidmatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KategoriPerkhidmatanResource extends Resource
{
    protected static ?string $model = KategoriPerkhidmatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Kategori Perkhidmatan';

    protected static ?string $pluralModelLabel = 'Kategori Perkhidmatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Kategori')
                    ->schema([
                        Forms\Components\TextInput::make('kod')
                            ->label('Kod')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: PER, BEK'),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama kategori perkhidmatan'),
                        Forms\Components\Textarea::make('penerangan')
                            ->label('Penerangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Penerangan ringkas'),
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
                Tables\Columns\TextColumn::make('kod')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kategori')
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
            ->defaultSort('kod');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriPerkhidmatans::route('/'),
            'create' => Pages\CreateKategoriPerkhidmatan::route('/create'),
            'edit' => Pages\EditKategoriPerkhidmatan::route('/{record}/edit'),
        ];
    }
}
