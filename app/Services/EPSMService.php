<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EPSMService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected int $retries;

    public function __construct()
    {
        $this->baseUrl = config('services.epsm.url', 'https://api.epsm.gov.my');
        $this->apiKey = config('services.epsm.api_key', '');
        $this->timeout = config('services.epsm.timeout', 10);
        $this->retries = config('services.epsm.retries', 3);
    }

    /**
     * Get user data from EPSM API by IC number
     *
     * @param string $noIc
     * @return array|null
     */
    public function getUserDataFromEPSM(string $noIc): ?array
    {
        // Clean IC number (remove dashes)
        $cleanIc = str_replace('-', '', $noIc);

        // Validate IC format (12 digits)
        if (!preg_match('/^\d{12}$/', $cleanIc)) {
            Log::warning('Invalid IC format', ['ic' => $noIc]);
            return null;
        }

        // Check cache first (24 hours)
        $cacheKey = "epsm_user_{$cleanIc}";
        if (Cache::has($cacheKey)) {
            Log::info('EPSM data retrieved from cache', ['ic' => $cleanIc]);
            return Cache::get($cacheKey);
        }

        try {
            // Call EPSM API with retry logic
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100, function ($exception, $request) {
                    Log::warning('EPSM API retry', [
                        'exception' => $exception->getMessage(),
                        'attempt' => $request->retries(),
                    ]);
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                })
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/pegawai/{$cleanIc}");

            if ($response->successful()) {
                $data = $response->json();

                // Transform EPSM response to our format
                $userData = $this->transformEPSMData($data);

                // Cache for 24 hours
                Cache::put($cacheKey, $userData, now()->addHours(24));

                Log::info('EPSM data retrieved successfully', ['ic' => $cleanIc]);

                return $userData;
            }

            if ($response->status() === 404) {
                Log::warning('User not found in EPSM', ['ic' => $cleanIc]);
                return null;
            }

            Log::error('EPSM API error', [
                'ic' => $cleanIc,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('EPSM API exception', [
                'ic' => $cleanIc,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Transform EPSM API response to our format
     *
     * @param array $data
     * @return array
     */
    protected function transformEPSMData(array $data): array
    {
        return [
            'no_kad_pengenalan' => $data['no_kp'] ?? null,
            'name' => $data['nama'] ?? null,
            'email' => $data['emel'] ?? null,
            'no_telefon' => $data['no_telefon'] ?? null,
            'jawatan' => $data['jawatan'] ?? null,
            'jabatan_kod' => $data['kod_jabatan'] ?? null,
            'jabatan_nama' => $data['nama_jabatan'] ?? null,
            'seksyen_kod' => $data['kod_bahagian'] ?? null,
            'seksyen_nama' => $data['nama_bahagian'] ?? null,
            'gred' => $data['gred'] ?? null,
            'status_perkhidmatan' => $data['status'] ?? 'aktif',
        ];
    }

    /**
     * Verify if user exists in EPSM
     *
     * @param string $noIc
     * @return bool
     */
    public function verifyUser(string $noIc): bool
    {
        $userData = $this->getUserDataFromEPSM($noIc);
        return $userData !== null;
    }

    /**
     * Clear cache for specific user
     *
     * @param string $noIc
     * @return void
     */
    public function clearCache(string $noIc): void
    {
        $cleanIc = str_replace('-', '', $noIc);
        $cacheKey = "epsm_user_{$cleanIc}";
        Cache::forget($cacheKey);

        Log::info('EPSM cache cleared', ['ic' => $cleanIc]);
    }

    /**
     * Get multiple users data (for bulk operations)
     *
     * @param array $noIcList
     * @return array
     */
    public function getBulkUserData(array $noIcList): array
    {
        $results = [];

        foreach ($noIcList as $noIc) {
            $results[$noIc] = $this->getUserDataFromEPSM($noIc);
        }

        return $results;
    }

    /**
     * Health check - verify EPSM API is accessible
     *
     * @return bool
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/health");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('EPSM health check failed', [
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get EPSM API statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $cachePattern = 'epsm_user_*';
        $cachedUsers = Cache::getRedis()->keys($cachePattern);

        return [
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'retries' => $this->retries,
            'cached_users' => count($cachedUsers ?? []),
            'api_status' => $this->healthCheck() ? 'healthy' : 'down',
        ];
    }
}
