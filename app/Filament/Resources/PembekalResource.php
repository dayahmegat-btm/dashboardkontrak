<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembekalResource\Pages;
use App\Filament\Resources\PembekalResource\RelationManagers;
use App\Models\Pembekal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembekalResource extends Resource
{
    protected static ?string $model = Pembekal::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Data Induk';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Pembekal';

    protected static ?string $pluralModelLabel = 'Pembekal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Syarikat')
                    ->schema([
                        Forms\Components\TextInput::make('nama_syarikat')
                            ->label('Nama Syarikat')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama penuh syarikat'),
                        Forms\Components\TextInput::make('no_pendaftaran')
                            ->label('No. Pendaftaran SSM')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: 202001234567'),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Alamat lengkap syarikat'),
                        Forms\Components\TextInput::make('no_telefon')
                            ->label('No. Telefon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 04-1234567'),
                        Forms\Components\TextInput::make('emel')
                            ->label('Emel')
                            ->email()
                            ->maxLength(100)
                            ->placeholder('emel@syarikat.com'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Maklumat Person In Charge (PIC)')
                    ->schema([
                        Forms\Components\TextInput::make('pic_nama')
                            ->label('Nama PIC')
                            ->maxLength(255)
                            ->placeholder('Nama wakil syarikat'),
                        Forms\Components\TextInput::make('pic_telefon')
                            ->label('No. Telefon PIC')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 012-3456789'),
                        Forms\Components\TextInput::make('pic_emel')
                            ->label('Emel PIC')
                            ->email()
                            ->maxLength(100)
                            ->placeholder('pic@syarikat.com')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_syarikat')
                    ->label('Nama Syarikat')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('no_pendaftaran')
                    ->label('No. Pendaftaran')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('no_telefon')
                    ->label('Telefon')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('emel')
                    ->label('Emel')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pic_nama')
                    ->label('PIC')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dipadam')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_syarikat');
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
            'index' => Pages\ListPembekals::route('/'),
            'create' => Pages\CreatePembekal::route('/create'),
            'edit' => Pages\EditPembekal::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
