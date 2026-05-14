<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatusKontrakResource\Pages;
use App\Models\StatusKontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StatusKontrakResource extends Resource
{
    protected static ?string $model = StatusKontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Status Kontrak';

    protected static ?string $pluralModelLabel = 'Status Kontrak';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Status')
                    ->schema([
                        Forms\Components\TextInput::make('kod')
                            ->label('Kod')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: AKTIF, TAMAT'),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Status')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama status kontrak'),
                        Forms\Components\ColorPicker::make('warna')
                            ->label('Warna')
                            ->nullable()
                            ->placeholder('Pilih warna untuk UI'),
                        Forms\Components\TextInput::make('urutan')
                            ->label('Urutan Paparan')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
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
                Tables\Columns\TextColumn::make('urutan')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kod')
                    ->label('Kod')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Status')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\ColorColumn::make('warna')
                    ->label('Warna'),
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
            ->defaultSort('urutan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusKontraks::route('/'),
            'create' => Pages\CreateStatusKontrak::route('/create'),
            'edit' => Pages\EditStatusKontrak::route('/{record}/edit'),
        ];
    }
}
