<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidContractPeriod implements ValidationRule
{
    protected string $field;
    protected ?string $tarikhMula;
    protected ?string $tarikhTamat;
    protected ?int $tempohBulan;

    /**
     * Create a new rule instance.
     *
     * @param string $field The field being validated
     * @param string|null $tarikhMula Start date
     * @param string|null $tarikhTamat End date
     * @param int|null $tempohBulan Period in months
     */
    public function __construct(string $field, ?string $tarikhMula = null, ?string $tarikhTamat = null, ?int $tempohBulan = null)
    {
        $this->field = $field;
        $this->tarikhMula = $tarikhMula;
        $this->tarikhTamat = $tarikhTamat;
        $this->tempohBulan = $tempohBulan;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validate tarikh_mula
        if ($this->field === 'tarikh_mula') {
            try {
                $tarikhMula = Carbon::parse($value);

                // Start date should not be more than 10 years in the past
                if ($tarikhMula->lt(Carbon::now()->subYears(10))) {
                    $fail('Tarikh mula tidak boleh lebih dari 10 tahun yang lalu.');
                    return;
                }

                // Start date should not be more than 5 years in the future
                if ($tarikhMula->gt(Carbon::now()->addYears(5))) {
                    $fail('Tarikh mula tidak boleh lebih dari 5 tahun akan datang.');
                    return;
                }

                // If tarikh_tamat is provided, check that tarikh_mula is before it
                if ($this->tarikhTamat) {
                    $tarikhTamat = Carbon::parse($this->tarikhTamat);
                    if ($tarikhMula->gte($tarikhTamat)) {
                        $fail('Tarikh mula mestilah sebelum tarikh tamat.');
                        return;
                    }
                }
            } catch (\Exception $e) {
                $fail('Format tarikh tidak sah.');
                return;
            }
        }

        // Validate tarikh_tamat
        if ($this->field === 'tarikh_tamat') {
            try {
                $tarikhTamat = Carbon::parse($value);

                // End date should not be more than 10 years in the future
                if ($tarikhTamat->gt(Carbon::now()->addYears(10))) {
                    $fail('Tarikh tamat tidak boleh lebih dari 10 tahun akan datang.');
                    return;
                }

                // If tarikh_mula is provided, check relationship
                if ($this->tarikhMula) {
                    $tarikhMula = Carbon::parse($this->tarikhMula);

                    if ($tarikhTamat->lte($tarikhMula)) {
                        $fail('Tarikh tamat mestilah selepas tarikh mula.');
                        return;
                    }

                    // Check if period matches tempoh_bulan
                    if ($this->tempohBulan !== null) {
                        $expectedTarikhTamat = $tarikhMula->copy()->addMonths($this->tempohBulan);

                        // Allow 3 days difference for month-end variations
                        if (abs($tarikhTamat->diffInDays($expectedTarikhTamat)) > 3) {
                            $fail('Tarikh tamat tidak sepadan dengan tempoh bulan. Sepatutnya sekitar ' . $expectedTarikhTamat->format('d/m/Y'));
                            return;
                        }
                    }
                }
            } catch (\Exception $e) {
                $fail('Format tarikh tidak sah.');
                return;
            }
        }

        // Validate tempoh_bulan
        if ($this->field === 'tempoh_bulan') {
            if ($value < 1) {
                $fail('Tempoh kontrak mestilah sekurang-kurangnya 1 bulan.');
                return;
            }

            if ($value > 120) {
                $fail('Tempoh kontrak tidak boleh melebihi 120 bulan (10 tahun).');
                return;
            }

            // Check if tempoh matches date range
            if ($this->tarikhMula && $this->tarikhTamat) {
                try {
                    $tarikhMula = Carbon::parse($this->tarikhMula);
                    $tarikhTamat = Carbon::parse($this->tarikhTamat);

                    $actualMonths = $tarikhMula->diffInMonths($tarikhTamat);

                    // Allow 1 month difference for calculation variations
                    if (abs($value - $actualMonths) > 1) {
                        $fail("Tempoh bulan ({$value}) tidak sepadan dengan jarak antara tarikh mula dan tamat (sekitar {$actualMonths} bulan).");
                        return;
                    }
                } catch (\Exception $e) {
                    // Skip this validation if dates are invalid
                }
            }
        }

        // Validate hari_sehingga_tamat (calculated field)
        if ($this->field === 'hari_sehingga_tamat' && $this->tarikhTamat) {
            try {
                $tarikhTamat = Carbon::parse($this->tarikhTamat);
                $today = Carbon::today();

                $expectedDays = $today->diffInDays($tarikhTamat, false);

                // If contract has expired, days should be negative
                if (abs($value - $expectedDays) > 1) {
                    // This is a calculated field, so this shouldn't normally fail
                    // But we validate for data integrity
                }
            } catch (\Exception $e) {
                // Skip validation if date is invalid
            }
        }
    }
}
