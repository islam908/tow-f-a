<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

class TOTPService
{
    public function generateCurrentCode(
        string $secret,
        ?int $timestamp = null,
        int $digits = 6,
        int $period = 30
    ): array {
        if ($digits < 4 || $digits > 10) {
            throw new InvalidArgumentException('OTP digits must be between 4 and 10.');
        }

        if ($period < 1) {
            throw new InvalidArgumentException('OTP period must be greater than zero.');
        }

        $binarySecret = $this->decodeBase32Secret($secret);
        $now = $timestamp ?? time();
        $counter = intdiv($now, $period);

        $binaryCounter = pack(
            'N2',
            ($counter >> 32) & 0xFFFFFFFF,
            $counter & 0xFFFFFFFF
        );

        $hash = hash_hmac('sha1', $binaryCounter, $binarySecret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $chunk = substr($hash, $offset, 4);
        $truncated = unpack('N', $chunk)[1] & 0x7FFFFFFF;
        $modulo = 10 ** $digits;

        $code = str_pad((string) ($truncated % $modulo), $digits, '0', STR_PAD_LEFT);

        $expiresIn = $period - ($now % $period);
        if ($expiresIn === 0) {
            $expiresIn = $period;
        }

        $expiresAt = CarbonImmutable::createFromTimestamp($now + $expiresIn);

        return [
            'code' => $code,
            'expires_in' => $expiresIn,
            'expires_at' => $expiresAt,
            'period' => $period,
            'digits' => $digits,
        ];
    }

    private function decodeBase32Secret(string $secret): string
    {
        $normalized = strtoupper(trim($secret));
        $normalized = str_replace([' ', '-'], '', $normalized);
        $normalized = rtrim($normalized, '=');

        if ($normalized === '') {
            throw new InvalidArgumentException('Secret key is empty.');
        }

        if (preg_match('/[^A-Z2-7]/', $normalized)) {
            throw new InvalidArgumentException('Secret key format is invalid.');
        }

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';

        foreach (str_split($normalized) as $character) {
            $position = strpos($alphabet, $character);

            if ($position === false) {
                throw new InvalidArgumentException('Secret key format is invalid.');
            }

            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        $bitsLength = strlen($bits);

        for ($index = 0; $index + 8 <= $bitsLength; $index += 8) {
            $binary .= chr(bindec(substr($bits, $index, 8)));
        }

        if ($binary === '') {
            throw new InvalidArgumentException('Secret key could not be decoded.');
        }

        return $binary;
    }
}
