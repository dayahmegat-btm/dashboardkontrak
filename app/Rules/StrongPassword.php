<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Minimum 8 characters
        if (strlen($value) < 8) {
            $fail('Kata laluan mestilah sekurang-kurangnya 8 aksara.');
            return;
        }

        // Must contain at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('Kata laluan mestilah mengandungi sekurang-kurangnya satu huruf besar.');
            return;
        }

        // Must contain at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $fail('Kata laluan mestilah mengandungi sekurang-kurangnya satu huruf kecil.');
            return;
        }

        // Must contain at least one number
        if (!preg_match('/[0-9]/', $value)) {
            $fail('Kata laluan mestilah mengandungi sekurang-kurangnya satu nombor.');
            return;
        }

        // Must contain at least one special character
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $value)) {
            $fail('Kata laluan mestilah mengandungi sekurang-kurangnya satu simbol khas (!@#$%^&*...).');
            return;
        }

        // Cannot contain common passwords
        $commonPasswords = [
            'password', 'Password123', '12345678', 'qwerty',
            'abc123', 'letmein', 'welcome', 'admin123',
        ];

        if (in_array($value, $commonPasswords)) {
            $fail('Kata laluan ini terlalu mudah diteka. Sila gunakan kata laluan yang lebih kuat.');
            return;
        }

        // Cannot contain sequential characters
        if (preg_match('/(?:abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i', $value)) {
            $fail('Kata laluan tidak boleh mengandungi aksara berturutan (contoh: abc, 123).');
            return;
        }

        if (preg_match('/(?:012|123|234|345|456|567|678|789)/', $value)) {
            $fail('Kata laluan tidak boleh mengandungi nombor berturutan (contoh: 123, 456).');
            return;
        }
    }
}
