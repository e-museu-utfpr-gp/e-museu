<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Minimal valid PNG bytes used as a small baseline when asserting composed QR posters are larger (GD can decode it).
 */
final class MinimalQrPng
{
    /**
     * @var non-empty-string
     */
    private const BASE64 =
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwA'
        . 'EhQGAhKmMIQ'
        . 'AAAABJRU5ErkJggg==';

    public static function binary(): string
    {
        $decoded = base64_decode(self::BASE64, true);
        if ($decoded === false) {
            throw new \RuntimeException('Invalid minimal QR PNG base64 fixture.');
        }

        return $decoded;
    }
}
