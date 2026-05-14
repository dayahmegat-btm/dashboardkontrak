<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorAuthentication extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.two-factor-authentication';

    protected static ?string $navigationGroup = 'Pengurusan Sistem';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Pengesahan Dua Faktor (2FA)';

    protected static ?string $navigationLabel = '2FA';

    public function getTitle(): string
    {
        return 'Pengesahan Dua Faktor (2FA)';
    }

    public function getHeading(): string
    {
        return 'Pengesahan Dua Faktor (2FA)';
    }

    public function getSubheading(): ?string
    {
        return 'Tambah lapisan keselamatan tambahan pada akaun anda dengan mengaktifkan pengesahan dua faktor.';
    }

    protected function getActions(): array
    {
        $user = Auth::user();

        if ($user->two_factor_secret) {
            // 2FA is enabled - show disable action
            return [
                Action::make('disable2FA')
                    ->label('Nyahaktifkan 2FA')
                    ->color('danger')
                    ->icon('heroicon-o-shield-exclamation')
                    ->requiresConfirmation()
                    ->modalHeading('Nyahaktifkan Pengesahan Dua Faktor?')
                    ->modalDescription('Adakah anda pasti ingin menyahaktifkan pengesahan dua faktor? Akaun anda akan kurang selamat.')
                    ->modalSubmitActionLabel('Ya, Nyahaktifkan')
                    ->form([
                        TextInput::make('password')
                            ->label('Kata Laluan Semasa')
                            ->password()
                            ->required()
                            ->rule(function () {
                                return function ($attribute, $value, $fail) {
                                    if (!Hash::check($value, Auth::user()->password)) {
                                        $fail('Kata laluan tidak tepat.');
                                    }
                                };
                            }),
                    ])
                    ->action(function () {
                        $user = Auth::user();
                        $user->update([
                            'two_factor_secret' => null,
                            'two_factor_recovery_codes' => null,
                            'two_factor_confirmed_at' => null,
                        ]);

                        Notification::make()
                            ->title('2FA Berjaya Dinyahaktifkan')
                            ->success()
                            ->send();

                        $this->redirect($this->getUrl());
                    }),

                Action::make('regenerateRecoveryCodes')
                    ->label('Jana Semula Kod Pemulihan')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalHeading('Jana Semula Kod Pemulihan')
                    ->modalDescription('Kod pemulihan lama anda akan tidak sah. Pastikan anda simpan kod baru.')
                    ->form([
                        TextInput::make('password')
                            ->label('Kata Laluan Semasa')
                            ->password()
                            ->required()
                            ->rule(function () {
                                return function ($attribute, $value, $fail) {
                                    if (!Hash::check($value, Auth::user()->password)) {
                                        $fail('Kata laluan tidak tepat.');
                                    }
                                };
                            }),
                    ])
                    ->action(function () {
                        $user = Auth::user();
                        $user->forceFill([
                            'two_factor_recovery_codes' => encrypt(json_encode(array_map(
                                fn () => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10),
                                range(1, 8)
                            ))),
                        ])->save();

                        Notification::make()
                            ->title('Kod Pemulihan Berjaya Dijana Semula')
                            ->success()
                            ->send();

                        $this->redirect($this->getUrl());
                    }),
            ];
        } else {
            // 2FA is not enabled - show enable action
            return [
                Action::make('enable2FA')
                    ->label('Aktifkan 2FA')
                    ->color('success')
                    ->icon('heroicon-o-shield-check')
                    ->form([
                        TextInput::make('password')
                            ->label('Kata Laluan Semasa')
                            ->password()
                            ->required()
                            ->helperText('Masukkan kata laluan semasa anda untuk mengaktifkan 2FA')
                            ->rule(function () {
                                return function ($attribute, $value, $fail) {
                                    if (!Hash::check($value, Auth::user()->password)) {
                                        $fail('Kata laluan tidak tepat.');
                                    }
                                };
                            }),
                    ])
                    ->action(function () {
                        $user = Auth::user();
                        $user->forceFill([
                            'two_factor_secret' => encrypt(\Laravel\Fortify\Actions\GenerateNewRecoveryCodes::class),
                            'two_factor_recovery_codes' => encrypt(json_encode(array_map(
                                fn () => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10),
                                range(1, 8)
                            ))),
                        ])->save();

                        Notification::make()
                            ->title('2FA Sedang Diaktifkan')
                            ->body('Sila imbas kod QR dengan aplikasi authenticator anda.')
                            ->success()
                            ->send();

                        $this->redirect($this->getUrl());
                    }),
            ];
        }
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}
