<x-filament-panels::page>
    @php
        $user = auth()->user();
        $twoFactorEnabled = !is_null($user->two_factor_secret);
    @endphp

    <div class="space-y-6">
        {{-- Status Card --}}
        <x-filament::section>
            <x-slot name="heading">
                Status 2FA
            </x-slot>

            <x-slot name="description">
                Pengesahan dua faktor {{ $twoFactorEnabled ? 'telah diaktifkan' : 'tidak aktif' }} untuk akaun anda.
            </x-slot>

            <div class="flex items-center gap-4">
                @if($twoFactorEnabled)
                    <div class="flex items-center gap-2 text-success-600 dark:text-success-400">
                        <x-heroicon-o-shield-check class="w-6 h-6"/>
                        <span class="font-semibold">2FA Aktif</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-shield-exclamation class="w-6 h-6"/>
                        <span class="font-semibold">2FA Tidak Aktif</span>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- QR Code Section (only show when 2FA is being set up) --}}
        @if($twoFactorEnabled && !$user->two_factor_confirmed_at)
            <x-filament::section>
                <x-slot name="heading">
                    Imbas Kod QR
                </x-slot>

                <x-slot name="description">
                    Imbas kod QR ini menggunakan aplikasi authenticator seperti Google Authenticator atau Authy.
                </x-slot>

                <div class="flex flex-col items-center gap-4">
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-lg">
                        {!! $user->twoFactorQrCodeSvg() !!}
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Atau masukkan kod ini secara manual:
                        </p>
                        <code class="mt-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded text-sm font-mono">
                            {{ decrypt($user->two_factor_secret) }}
                        </code>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Recovery Codes Section --}}
        @if($twoFactorEnabled && $user->two_factor_recovery_codes)
            <x-filament::section>
                <x-slot name="heading">
                    Kod Pemulihan
                </x-slot>

                <x-slot name="description">
                    Simpan kod pemulihan ini di tempat yang selamat. Anda boleh menggunakannya untuk mengakses akaun anda jika anda kehilangan akses kepada peranti authenticator anda.
                </x-slot>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-2 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                        @foreach(json_decode(decrypt($user->two_factor_recovery_codes)) as $code)
                            <div class="font-mono text-sm p-2 bg-white dark:bg-gray-800 rounded">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>

                    <x-filament::badge color="warning" icon="heroicon-o-exclamation-triangle">
                        Pastikan anda menyimpan kod ini dengan selamat. Setiap kod hanya boleh digunakan sekali.
                    </x-filament::badge>
                </div>
            </x-filament::section>
        @endif

        {{-- Instructions Section --}}
        @if(!$twoFactorEnabled)
            <x-filament::section>
                <x-slot name="heading">
                    Mengapa Mengaktifkan 2FA?
                </x-slot>

                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <p>
                        Pengesahan dua faktor (2FA) menambah lapisan keselamatan tambahan pada akaun anda.
                        Walaupun seseorang mengetahui kata laluan anda, mereka tidak boleh log masuk tanpa kod dari peranti anda.
                    </p>

                    <h4 class="text-base font-semibold mt-4 mb-2">Cara Menggunakan 2FA:</h4>
                    <ol class="space-y-2">
                        <li>Klik butang "Aktifkan 2FA" di atas</li>
                        <li>Muat turun aplikasi authenticator (Google Authenticator, Authy, dll.)</li>
                        <li>Imbas kod QR yang akan dipaparkan</li>
                        <li>Simpan kod pemulihan di tempat yang selamat</li>
                        <li>Setiap kali log masuk, anda perlu masukkan kod dari aplikasi authenticator</li>
                    </ol>

                    <h4 class="text-base font-semibold mt-4 mb-2">Aplikasi Authenticator Yang Disyorkan:</h4>
                    <ul class="space-y-1">
                        <li><strong>Google Authenticator</strong> - iOS & Android</li>
                        <li><strong>Microsoft Authenticator</strong> - iOS & Android</li>
                        <li><strong>Authy</strong> - iOS, Android & Desktop</li>
                    </ul>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <x-slot name="heading">
                    Tips Keselamatan
                </x-slot>

                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <ul class="space-y-2">
                        <li>Simpan kod pemulihan anda di tempat yang selamat (contoh: password manager, lokasi fizikal yang selamat)</li>
                        <li>Jangan kongsikan kod pemulihan dengan sesiapa</li>
                        <li>Jika anda tukar telefon, pastikan anda pindahkan aplikasi authenticator atau setup semula 2FA</li>
                        <li>Jana semula kod pemulihan jika anda rasa ia telah terdedah</li>
                    </ul>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
