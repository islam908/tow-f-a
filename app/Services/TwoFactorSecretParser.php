<?php

namespace App\Services;

use InvalidArgumentException;

class TwoFactorSecretParser
{
    public function normalize(string $input): string
    {
        $value = trim($input);

        if ($value === '') {
            throw new InvalidArgumentException('المفتاح السري مطلوب.');
        }

        $secret = $this->extractSecretFromQrPayload($value) ?? $value;
        $secret = strtoupper($secret);
        $secret = str_replace([' ', '-'], '', $secret);
        $secret = rtrim($secret, '=');

        if ($secret === '') {
            throw new InvalidArgumentException('تعذر استخراج 2FA Seed صالح من النص أو رمز QR.');
        }

        if (preg_match('/^[A-Z2-7]+$/', $secret) !== 1) {
            throw new InvalidArgumentException('تعذر استخراج 2FA Seed صالح من النص أو رمز QR.');
        }

        return $secret;
    }

    private function extractSecretFromQrPayload(string $value): ?string
    {
        if (preg_match('/(?:[?&]|^)secret=([^&]+)/i', $value, $matches) !== 1) {
            return null;
        }

        $decoded = urldecode($matches[1]);
        $decoded = trim($decoded);

        return $decoded === '' ? null : $decoded;
    }
}
