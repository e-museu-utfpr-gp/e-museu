<?php

declare(strict_types=1);

namespace App\Support\Mail;

class EmailVerificationCode
{
    public const int TTL_MINUTES = 15;

    public static function generatePlainCode(): string
    {
        return str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Pending code hashes in session depend on `config('app.key')`; rotating the application key invalidates
     * in-flight verifications.
     */
    public static function hashPlainCode(string $code): string
    {
        return hash('sha256', $code . config('app.key'));
    }

    /**
     * @return array{code: string, hash: string}
     */
    public static function generate(): array
    {
        $code = self::generatePlainCode();

        return [
            'code' => $code,
            'hash' => self::hashPlainCode($code),
        ];
    }
}
