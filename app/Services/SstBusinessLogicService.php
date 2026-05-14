<?php

namespace App\Services;

use App\Models\DaftarSst;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SstBusinessLogicService
{
    /**
     * Calculate tarikh_tamat based on tarikh_mula and tempoh_bulan
     *
     * @param string|Carbon $tarikhMula
     * @param int $tempohBulan
     * @return Carbon
     */
    public function calculateTarikhTamat($tarikhMula, int $tempohBulan): Carbon
    {
        $startDate = $tarikhMula instanceof Carbon ? $tarikhMula : Carbon::parse($tarikhMula);
        return $startDate->copy()->addMonths($tempohBulan)->subDay();
    }

    /**
     * Calculate baki_kontrak based on nilai_kontrak and nilai_komitmen
     *
     * @param float $nilaiKontrak
     * @param float $nilaiKomitmen
     * @return float
     */
    public function calculateBakiKontrak(float $nilaiKontrak, float $nilaiKomitmen): float
    {
        return round($nilaiKontrak - $nilaiKomitmen, 2);
    }

    /**
     * Calculate hari_sehingga_tamat based on tarikh_tamat
     *
     * @param string|Carbon $tarikhTamat
     * @return int Negative if expired
     */
    public function calculateHariSehingga Tamat($tarikhTamat): int
    {
        $endDate = $tarikhTamat instanceof Carbon ? $tarikhTamat : Carbon::parse($tarikhTamat);
        $today = Carbon::today();

        return $today->diffInDays($endDate, false);
    }

    /**
     * Generate next SST number for a given year
     *
     * @param int|null $year
     * @return string
     */
    public function generateSstNumber(?int $year = null): string
    {
        $year = $year ?? date('Y');

        // Get the last SST number for this year
        $lastSst = DaftarSst::where('no_sst', 'LIKE', "SST/{$year}/%")
            ->orderBy('no_sst', 'desc')
            ->first();

        if ($lastSst) {
            // Extract sequence number
            preg_match('/^SST\/\d{4}\/(\d{4})$/', $lastSst->no_sst, $matches);
            $lastSequence = isset($matches[1]) ? (int) $matches[1] : 0;
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        return sprintf('SST/%d/%04d', $year, $nextSequence);
    }

    /**
     * Check if SST is expiring soon
     *
     * @param DaftarSst $sst
     * @param int $days Threshold in days (default: 90)
     * @return bool
     */
    public function isExpiringSoon(DaftarSst $sst, int $days = 90): bool
    {
        if (!$sst->hari_sehingga_tamat) {
            return false;
        }

        return $sst->hari_sehingga_tamat > 0 && $sst->hari_sehingga_tamat <= $days;
    }

    /**
     * Check if SST has expired
     *
     * @param DaftarSst $sst
     * @return bool
     */
    public function hasExpired(DaftarSst $sst): bool
    {
        return $sst->hari_sehingga_tamat !== null && $sst->hari_sehingga_tamat < 0;
    }

    /**
     * Get expiry status with color code
     *
     * @param int|null $hariSehingga Tamat
     * @return array ['status' => string, 'color' => string, 'icon' => string]
     */
    public function getExpiryStatus(?int $hariSehingga Tamat): array
    {
        if ($hariSehingga Tamat === null) {
            return [
                'status' => 'Tiada Maklumat',
                'color' => 'gray',
                'icon' => 'heroicon-o-question-mark-circle',
            ];
        }

        if ($hariSehingga Tamat < 0) {
            return [
                'status' => 'Tamat Tempoh',
                'color' => 'danger',
                'icon' => 'heroicon-o-x-circle',
            ];
        }

        if ($hariSehingga Tamat <= 7) {
            return [
                'status' => 'Kritikal (' . $hariSehingga Tamat . ' hari)',
                'color' => 'danger',
                'icon' => 'heroicon-o-exclamation-triangle',
            ];
        }

        if ($hariSehingga Tamat <= 30) {
            return [
                'status' => 'Amaran (' . $hariSehingga Tamat . ' hari)',
                'color' => 'warning',
                'icon' => 'heroicon-o-exclamation-circle',
            ];
        }

        if ($hariSehingga Tamat <= 90) {
            return [
                'status' => 'Makluman (' . $hariSehingga Tamat . ' hari)',
                'color' => 'info',
                'icon' => 'heroicon-o-information-circle',
            ];
        }

        return [
            'status' => 'Aktif (' . $hariSehingga Tamat . ' hari)',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
        ];
    }

    /**
     * Check if nilai_komitmen is unusually low compared to nilai_kontrak
     *
     * @param float $nilaiKontrak
     * @param float $nilaiKomitmen
     * @param float $threshold Percentage threshold (default: 10%)
     * @return bool
     */
    public function isCommitmentTooLow(float $nilaiKontrak, float $nilaiKomitmen, float $threshold = 10.0): bool
    {
        if ($nilaiKontrak <= 0) {
            return false;
        }

        $percentage = ($nilaiKomitmen / $nilaiKontrak) * 100;

        return $nilaiKomitmen > 0 && $percentage < $threshold;
    }

    /**
     * Validate SST data before save
     *
     * @param array $data
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public function validateSstData(array $data): array
    {
        $errors = [];
        $warnings = [];

        // Required fields check
        $requiredFields = ['no_sst', 'tajuk', 'jabatan_id', 'tarikh_mula', 'tempoh_bulan', 'nilai_kontrak'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '{$field}' adalah wajib.";
            }
        }

        // Financial validation
        if (isset($data['nilai_kontrak'], $data['nilai_komitmen'])) {
            if ($data['nilai_komitmen'] > $data['nilai_kontrak']) {
                $errors[] = 'Nilai komitmen tidak boleh melebihi nilai kontrak.';
            }

            if ($this->isCommitmentTooLow($data['nilai_kontrak'], $data['nilai_komitmen'])) {
                $warnings[] = 'Nilai komitmen adalah rendah berbanding nilai kontrak (kurang dari 10%).';
            }
        }

        // Date validation
        if (isset($data['tarikh_mula'], $data['tarikh_tamat'])) {
            try {
                $tarikhMula = Carbon::parse($data['tarikh_mula']);
                $tarikhTamat = Carbon::parse($data['tarikh_tamat']);

                if ($tarikhTamat->lte($tarikhMula)) {
                    $errors[] = 'Tarikh tamat mestilah selepas tarikh mula.';
                }
            } catch (\Exception $e) {
                $errors[] = 'Format tarikh tidak sah.';
            }
        }

        // Category validation
        if (!empty($data['is_kategori_1']) && !empty($data['is_kategori_2'])) {
            $warnings[] = 'SST ditandakan sebagai kedua-dua Kategori 1 dan Kategori 2. Sila pastikan ini betul.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check if SST requires renewal/extension
     *
     * @param DaftarSst $sst
     * @param int $noticeDay s Number of days before expiry to flag for renewal
     * @return bool
     */
    public function requiresRenewal(DaftarSst $sst, int $noticeDays = 90): bool
    {
        return $this->isExpiringSoon($sst, $noticeDays) && !$this->hasExpired($sst);
    }

    /**
     * Get SST financial summary
     *
     * @param DaftarSst $sst
     * @return array
     */
    public function getFinancialSummary(DaftarSst $sst): array
    {
        $commitmentPercentage = $sst->nilai_kontrak > 0
            ? round(($sst->nilai_komitmen / $sst->nilai_kontrak) * 100, 2)
            : 0;

        $balancePercentage = $sst->nilai_kontrak > 0
            ? round(($sst->baki_kontrak / $sst->nilai_kontrak) * 100, 2)
            : 0;

        return [
            'nilai_kontrak' => $sst->nilai_kontrak,
            'nilai_komitmen' => $sst->nilai_komitmen,
            'baki_kontrak' => $sst->baki_kontrak,
            'commitment_percentage' => $commitmentPercentage,
            'balance_percentage' => $balancePercentage,
            'is_fully_committed' => $commitmentPercentage >= 100,
            'is_low_commitment' => $this->isCommitmentTooLow($sst->nilai_kontrak, $sst->nilai_komitmen),
        ];
    }

    /**
     * Calculate contract utilization rate
     *
     * @param DaftarSst $sst
     * @return float Percentage (0-100)
     */
    public function getUtilizationRate(DaftarSst $sst): float
    {
        if ($sst->nilai_kontrak <= 0) {
            return 0;
        }

        return round(($sst->nilai_komitmen / $sst->nilai_kontrak) * 100, 2);
    }

    /**
     * Log SST business event
     *
     * @param string $event
     * @param DaftarSst $sst
     * @param array $extraData
     * @return void
     */
    public function logBusinessEvent(string $event, DaftarSst $sst, array $extraData = []): void
    {
        Log::info("SST Business Event: {$event}", array_merge([
            'sst_id' => $sst->id,
            'no_sst' => $sst->no_sst,
            'nilai_kontrak' => $sst->nilai_kontrak,
            'hari_sehingga_tamat' => $sst->hari_sehingga_tamat,
        ], $extraData));
    }
}
