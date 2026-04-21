<?php

declare(strict_types=1);

namespace App\Support;

class StringHelper
{
    /**
     * Remove accents from a string (e.g. "Café" → "Cafe").
     * Uses Intl Transliterator when available, otherwise a character map.
     */
    public static function removeAccent(string $string): string
    {
        if ($string === '') {
            return '';
        }

        $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');

        if ($transliterator !== null) {
            $result = $transliterator->transliterate($string);

            return $result !== false ? $result : $string;
        }

        return strtr($string, self::ACCENT_MAP);
    }

    /**
     * First up to {@code $maxLength} characters (multibyte safe).
     */
    public static function mbTakePrefix(string $value, int $maxLength): string
    {
        if ($maxLength < 1 || $value === '') {
            return '';
        }

        $len = mb_strlen($value);

        return mb_substr($value, 0, min($maxLength, $len));
    }

    /**
     * Last up to {@code $maxLength} characters (multibyte safe).
     */
    public static function mbTakeSuffix(string $value, int $maxLength): string
    {
        if ($maxLength < 1 || $value === '') {
            return '';
        }

        $len = mb_strlen($value);
        $take = min($maxLength, $len);

        return mb_substr($value, $len - $take, $take);
    }

    /** @var array<string, string> Fallback when Intl is not available */
    private const ACCENT_MAP = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
        'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
        'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ñ' => 'n', 'Ñ' => 'N', 'ç' => 'c', 'Ç' => 'C',
    ];
}
