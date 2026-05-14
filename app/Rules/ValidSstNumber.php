<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSstNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // SST number format: SST/YYYY/XXXX
        // Example: SST/2026/0001

        if (empty($value)) {
            $fail('No. SST diperlukan.');
            return;
        }

        // Check format pattern
        if (!preg_match('/^SST\/\d{4}\/\d{4}$/', $value)) {
            $fail('Format No. SST tidak sah. Format yang betul: SST/YYYY/XXXX (contoh: SST/2026/0001)');
            return;
        }

        // Extract year and validate it's reasonable
        preg_match('/^SST\/(\d{4})\/\d{4}$/', $value, $matches);
        $year = (int) $matches[1];
        $currentYear = (int) date('Y');

        // Year should be between 2020 and 5 years from now
        if ($year < 2020 || $year > ($currentYear + 5)) {
            $fail("Tahun dalam No. SST ({$year}) tidak sah. Tahun mestilah antara 2020 dan " . ($currentYear + 5));
            return;
        }

        // Extract sequence number
        preg_match('/^SST\/\d{4}\/(\d{4})$/', $value, $matches);
        $sequence = (int) $matches[1];

        // Sequence must be greater than 0
        if ($sequence < 1) {
            $fail('Nombor urutan dalam No. SST mestilah bermula dari 0001');
            return;
        }
    }
}
