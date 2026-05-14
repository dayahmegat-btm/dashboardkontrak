<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidContractFinancials implements ValidationRule
{
    protected string $field;
    protected ?float $nilaiKontrak;
    protected ?float $nilaiKomitmen;

    /**
     * Create a new rule instance.
     *
     * @param string $field The field being validated (nilai_kontrak, nilai_komitmen, or baki_kontrak)
     * @param float|null $nilaiKontrak The contract value
     * @param float|null $nilaiKomitmen The commitment value
     */
    public function __construct(string $field, ?float $nilaiKontrak = null, ?float $nilaiKomitmen = null)
    {
        $this->field = $field;
        $this->nilaiKontrak = $nilaiKontrak;
        $this->nilaiKomitmen = $nilaiKomitmen;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // All financial values must be non-negative
        if ($value < 0) {
            $fail('Nilai kewangan mestilah positif (tidak boleh negatif).');
            return;
        }

        // Maximum contract value: RM 100 million
        if ($this->field === 'nilai_kontrak' && $value > 100000000) {
            $fail('Nilai kontrak melebihi had maksimum (RM 100 juta).');
            return;
        }

        // Nilai komitmen cannot exceed nilai kontrak
        if ($this->field === 'nilai_komitmen' && $this->nilaiKontrak !== null) {
            if ($value > $this->nilaiKontrak) {
                $fail('Nilai komitmen tidak boleh melebihi nilai kontrak (RM ' . number_format($this->nilaiKontrak, 2) . ').');
                return;
            }
        }

        // Baki kontrak validation
        if ($this->field === 'baki_kontrak' && $this->nilaiKontrak !== null && $this->nilaiKomitmen !== null) {
            $expectedBaki = $this->nilaiKontrak - $this->nilaiKomitmen;

            // Allow small floating point difference (0.01)
            if (abs($value - $expectedBaki) > 0.01) {
                $fail('Baki kontrak tidak sepadan. Sepatutnya: RM ' . number_format($expectedBaki, 2) . ' (Nilai Kontrak - Nilai Komitmen)');
                return;
            }
        }

        // Warn if nilai komitmen is very low compared to nilai kontrak (< 10%)
        if ($this->field === 'nilai_komitmen' && $this->nilaiKontrak !== null && $this->nilaiKontrak > 0) {
            $percentage = ($value / $this->nilaiKontrak) * 100;

            if ($percentage < 10 && $value > 0) {
                // This is just a business rule check, not a hard validation failure
                // We'll handle warnings in the service layer
            }
        }
    }
}
