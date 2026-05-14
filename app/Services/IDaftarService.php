<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IDaftarService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = config('services.idaftar.url', 'https://api.idaftar.gov.my');
        $this->apiKey = config('services.idaftar.api_key', '');
        $this->timeout = config('services.idaftar.timeout', 10);
        $this->cacheTtl = config('services.idaftar.cache_ttl', 10080); // 7 days in minutes
    }

    /**
     * Get supplier data from iDaftar API by registration number
     *
     * @param string $noSijil Registration/Certificate number
     * @return array|null
     */
    public function getSupplierData(string $noSijil): ?array
    {
        // Clean registration number (remove spaces and special chars)
        $cleanNo = preg_replace('/[^A-Za-z0-9]/', '', $noSijil);

        // Validate format (alphanumeric, 5-20 chars)
        if (!preg_match('/^[A-Za-z0-9]{5,20}$/', $cleanNo)) {
            Log::warning('Invalid registration number format', ['no_sijil' => $noSijil]);
            return null;
        }

        // Check cache first
        $cacheKey = "idaftar_supplier_{$cleanNo}";
        if (Cache::has($cacheKey)) {
            Log::info('iDaftar data retrieved from cache', ['no_sijil' => $cleanNo]);
            return Cache::get($cacheKey);
        }

        try {
            // Call iDaftar API with retry logic
            $response = Http::timeout($this->timeout)
                ->retry(3, 100, function ($exception, $request) {
                    Log::warning('iDaftar API retry', [
                        'exception' => $exception->getMessage(),
                        'attempt' => $request->retries(),
                    ]);
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                })
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/pembekal/{$cleanNo}");

            if ($response->successful()) {
                $data = $response->json();

                // Transform iDaftar response to our format
                $supplierData = $this->transformIDaftarData($data);

                // Cache for configured TTL
                Cache::put($cacheKey, $supplierData, now()->addMinutes($this->cacheTtl));

                Log::info('iDaftar data retrieved successfully', ['no_sijil' => $cleanNo]);

                return $supplierData;
            }

            if ($response->status() === 404) {
                Log::warning('Supplier not found in iDaftar', ['no_sijil' => $cleanNo]);
                return null;
            }

            Log::error('iDaftar API error', [
                'no_sijil' => $cleanNo,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('iDaftar API exception', [
                'no_sijil' => $cleanNo,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Search suppliers by name or registration number
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchSuppliers(string $query, int $limit = 10): array
    {
        $cacheKey = "idaftar_search_" . md5($query . $limit);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/pembekal/search", [
                    'q' => $query,
                    'limit' => $limit,
                ]);

            if ($response->successful()) {
                $results = $response->json('data', []);

                // Transform each result
                $transformedResults = array_map(
                    fn($item) => $this->transformIDaftarData($item),
                    $results
                );

                // Cache search results for 1 hour
                Cache::put($cacheKey, $transformedResults, now()->addHour());

                return $transformedResults;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('iDaftar search exception', [
                'query' => $query,
                'exception' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get supplier financial information
     *
     * @param string $noSijil
     * @return array|null
     */
    public function getSupplierFinancialInfo(string $noSijil): ?array
    {
        $cleanNo = preg_replace('/[^A-Za-z0-9]/', '', $noSijil);
        $cacheKey = "idaftar_financial_{$cleanNo}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/pembekal/{$cleanNo}/kewangan");

            if ($response->successful()) {
                $data = $response->json();

                $financialInfo = [
                    'modal_berbayar' => $data['paid_capital'] ?? 0,
                    'nilai_kontrak_semasa' => $data['current_contracts_value'] ?? 0,
                    'bilangan_kontrak_aktif' => $data['active_contracts_count'] ?? 0,
                    'penarafan_kewangan' => $data['financial_rating'] ?? null,
                    'had_tender' => $data['tender_limit'] ?? 0,
                ];

                // Cache for 24 hours
                Cache::put($cacheKey, $financialInfo, now()->addHours(24));

                return $financialInfo;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('iDaftar financial info exception', [
                'no_sijil' => $cleanNo,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify if supplier is registered and active in iDaftar
     *
     * @param string $noSijil
     * @return bool
     */
    public function verifySupplier(string $noSijil): bool
    {
        $supplierData = $this->getSupplierData($noSijil);

        if ($supplierData === null) {
            return false;
        }

        // Check if supplier is active
        return ($supplierData['status'] ?? '') === 'Aktif';
    }

    /**
     * Get supplier categories/classifications
     *
     * @param string $noSijil
     * @return array
     */
    public function getSupplierCategories(string $noSijil): array
    {
        $cleanNo = preg_replace('/[^A-Za-z0-9]/', '', $noSijil);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/pembekal/{$cleanNo}/kategori");

            if ($response->successful()) {
                return $response->json('data', []);
            }

            return [];

        } catch (\Exception $e) {
            Log::error('iDaftar categories exception', [
                'no_sijil' => $cleanNo,
                'exception' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Transform iDaftar API response to our format
     *
     * @param array $data
     * @return array
     */
    protected function transformIDaftarData(array $data): array
    {
        return [
            'no_pendaftaran' => $data['registration_no'] ?? $data['no_pendaftaran'] ?? null,
            'nama_syarikat' => $data['company_name'] ?? $data['nama_syarikat'] ?? null,
            'jenis_pembekal' => $data['supplier_type'] ?? $data['jenis_pembekal'] ?? 'Syarikat',
            'no_ssm' => $data['ssm_no'] ?? $data['no_ssm'] ?? null,
            'alamat' => $data['address'] ?? $data['alamat'] ?? null,
            'bandar' => $data['city'] ?? $data['bandar'] ?? null,
            'negeri' => $data['state'] ?? $data['negeri'] ?? null,
            'poskod' => $data['postcode'] ?? $data['poskod'] ?? null,
            'no_telefon' => $data['phone'] ?? $data['no_telefon'] ?? null,
            'no_faks' => $data['fax'] ?? $data['no_faks'] ?? null,
            'emel' => $data['email'] ?? $data['emel'] ?? null,
            'laman_web' => $data['website'] ?? $data['laman_web'] ?? null,
            'pic_nama' => $data['contact_person'] ?? $data['pic_nama'] ?? null,
            'pic_telefon' => $data['contact_phone'] ?? $data['pic_telefon'] ?? null,
            'pic_emel' => $data['contact_email'] ?? $data['pic_emel'] ?? null,
            'status' => $data['status'] ?? 'Tidak Diketahui',
            'tarikh_luput' => $data['expiry_date'] ?? $data['tarikh_luput'] ?? null,
            'gred' => $data['grade'] ?? $data['gred'] ?? null,
            'kategori' => $data['categories'] ?? $data['kategori'] ?? [],
        ];
    }

    /**
     * Clear cache for specific supplier
     *
     * @param string $noSijil
     * @return void
     */
    public function clearCache(string $noSijil): void
    {
        $cleanNo = preg_replace('/[^A-Za-z0-9]/', '', $noSijil);
        $cacheKey = "idaftar_supplier_{$cleanNo}";
        Cache::forget($cacheKey);

        // Also clear financial cache
        Cache::forget("idaftar_financial_{$cleanNo}");

        Log::info('iDaftar cache cleared', ['no_sijil' => $cleanNo]);
    }

    /**
     * Clear all iDaftar caches
     *
     * @return void
     */
    public function clearAllCache(): void
    {
        // This would need Redis or another cache store with pattern support
        // For now, just log the action
        Log::info('iDaftar all cache clear requested');
    }

    /**
     * Health check - verify iDaftar API is accessible
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
            Log::error('iDaftar health check failed', [
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get iDaftar API statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'cache_ttl_minutes' => $this->cacheTtl,
            'api_status' => $this->healthCheck() ? 'healthy' : 'down',
        ];
    }

    /**
     * Validate supplier registration number format
     *
     * @param string $noSijil
     * @return bool
     */
    public function isValidRegistrationFormat(string $noSijil): bool
    {
        $cleanNo = preg_replace('/[^A-Za-z0-9]/', '', $noSijil);
        return preg_match('/^[A-Za-z0-9]{5,20}$/', $cleanNo) === 1;
    }
}
