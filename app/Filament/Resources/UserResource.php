<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Rules\StrongPassword;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Pengurusan Sistem';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maklumat Peribadi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Penuh')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama penuh pengguna'),
                        Forms\Components\TextInput::make('no_kad_pengenalan')
                            ->label('No. Kad Pengenalan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(12)
                            ->placeholder('Contoh: 900101011234')
                            ->mask('999999999999')
                            ->helperText('12 digit tanpa sengkang')
                            ->rules(['regex:/^\d{12}$/']),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mel')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('nama@suk.kedah.gov.my')
                            ->suffixIcon('heroicon-o-envelope'),
                        Forms\Components\TextInput::make('no_telefon')
                            ->label('No. Telefon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('04-1234567 atau 012-3456789')
                            ->suffixIcon('heroicon-o-phone'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Maklumat Organisasi')
                    ->schema([
                        Forms\Components\Select::make('jabatan_id')
                            ->label('Jabatan')
                            ->relationship('jabatan', 'nama_jabatan')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('seksyen_unit_id', null))
                            ->helperText('Pilih jabatan pengguna'),
                        Forms\Components\Select::make('seksyen_unit_id')
                            ->label('Seksyen/Unit')
                            ->relationship('seksyenUnit', 'nama_seksyen_unit', fn (Builder $query, callable $get) =>
                                $query->when($get('jabatan_id'), fn ($q, $jabatan) => $q->where('jabatan_id', $jabatan))
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih seksyen/unit pengguna'),
                        Forms\Components\TextInput::make('jawatan')
                            ->label('Jawatan')
                            ->maxLength(255)
                            ->placeholder('Contoh: Pegawai Perolehan'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Peranan & Akses')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Peranan')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih peranan pengguna (boleh pilih lebih dari satu)')
                            ->options(function () {
                                return Role::all()->pluck('name', 'id')->map(function ($name) {
                                    return match($name) {
                                        'super-admin' => 'Super Admin',
                                        'admin' => 'Admin',
                                        'sk-exec' => 'Eksekutif SK',
                                        'pengarah' => 'Pengarah',
                                        'ketua-unit' => 'Ketua Unit',
                                        'pic' => 'PIC',
                                        'audit' => 'Audit',
                                        default => ucfirst($name),
                                    };
                                });
                            }),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Akaun Aktif')
                            ->default(true)
                            ->helperText('Matikan untuk menyekat akses pengguna')
                            ->inline(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kata Laluan')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Kata Laluan')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rules([new StrongPassword()])
                            ->maxLength(255)
                            ->placeholder('Kata laluan sekurang-kurangnya 8 aksara')
                            ->helperText('Mesti mengandungi huruf besar, huruf kecil, nombor dan simbol'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Sahkan Kata Laluan')
                            ->password()
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->same('password')
                            ->placeholder('Taip semula kata laluan'),
                        Forms\Components\Toggle::make('force_password_change')
                            ->label('Paksa Tukar Kata Laluan')
                            ->default(false)
                            ->helperText('Pengguna akan diminta tukar kata laluan pada log masuk seterusnya')
                            ->inline(false),
                    ])
                    ->columns(3)
                    ->visible(fn (string $context): bool => $context === 'create' || auth()->user()->can('update', User::class)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mel')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('no_kad_pengenalan')
                    ->label('No. K/P')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('no_telefon')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-')
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->jabatan?->nama_jabatan),
                Tables\Columns\TextColumn::make('seksyenUnit.nama_seksyen_unit')
                    ->label('Seksyen/Unit')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jawatan')
                    ->label('Jawatan')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peranan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super-admin' => 'danger',
                        'admin' => 'warning',
                        'pengarah' => 'success',
                        'ketua-unit' => 'info',
                        'pic' => 'primary',
                        'audit' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'super-admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'sk-exec' => 'Eksekutif SK',
                        'pengarah' => 'Pengarah',
                        'ketua-unit' => 'Ketua Unit',
                        'pic' => 'PIC',
                        'audit' => 'Audit',
                        default => ucfirst($state),
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('E-mel Disahkan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Log Masuk Terakhir')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Belum log masuk'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicipta')
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
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Peranan')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => match($record->name) {
                        'super-admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'sk-exec' => 'Eksekutif SK',
                        'pengarah' => 'Pengarah',
                        'ketua-unit' => 'Ketua Unit',
                        'pic' => 'PIC',
                        'audit' => 'Audit',
                        default => ucfirst($record->name),
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Pengguna')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                Tables\Filters\Filter::make('email_verified')
                    ->label('E-mel Disahkan')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Kata Laluan')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Kata Laluan')
                    ->modalDescription(fn ($record) => "Reset kata laluan untuk {$record->name}?")
                    ->action(function (User $record) {
                        $record->update([
                            'force_password_change' => true,
                        ]);
                    })
                    ->successNotificationTitle('Kata laluan telah direset. Pengguna akan diminta tukar kata laluan.'),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (User $record) => $record->is_active ? 'Nyahaktifkan' : 'Aktifkan')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'is_active' => !$record->is_active,
                        ]);
                    })
                    ->successNotificationTitle('Status pengguna telah dikemaskini'),
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Padam'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nyahaktifkan')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Padam Dipilih'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Pulihkan Dipilih'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Padam Kekal'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Tiada Pengguna')
            ->emptyStateDescription('Tambah pengguna baru untuk mula menggunakan sistem.')
            ->emptyStateIcon('heroicon-o-users');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
