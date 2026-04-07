<?php

namespace App\Support\Text;

use App\Support\StringHelper;

/**
 * Builds a short uppercase segment from a human title: up to N leading characters of the first
 * "alnum word" and up to N trailing characters of the last (whitespace-separated tokens stripped
 * to Unicode letters and numbers only). Used for identification codes and similar compact labels.
 */
final class TitleLetterCompactSegment
{
    public static function fromRawTitle(string $rawTitle, int $edgeLetters = 2): string
    {
        if ($edgeLetters < 1) {
            $edgeLetters = 1;
        }

        $normalized = strtoupper(StringHelper::removeAccent(trim($rawTitle)));
        if ($normalized === '') {
            return '';
        }

        $tokens = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        if ($tokens === false) {
            return '';
        }

        /** @var list<string> $alnumWords */
        $alnumWords = [];
        foreach ($tokens as $token) {
            $alnumOnly = preg_replace('/[^\p{L}\p{N}]/u', '', $token) ?? '';
            if ($alnumOnly !== '') {
                $alnumWords[] = $alnumOnly;
            }
        }

        if (! isset($alnumWords[0])) {
            return '';
        }

        $first = $alnumWords[0];
        $last = $alnumWords[count($alnumWords) - 1];

        if ($first === $last) {
            return self::singleWordSegment($first, $edgeLetters);
        }

        return StringHelper::mbTakePrefix($first, $edgeLetters)
            . StringHelper::mbTakeSuffix($last, $edgeLetters);
    }

    private static function singleWordSegment(string $word, int $edgeLetters): string
    {
        $len = mb_strlen($word);
        if ($len <= 0) {
            return '';
        }
        if ($len === 1) {
            return $word;
        }
        if ($len <= $edgeLetters) {
            return $word;
        }

        return StringHelper::mbTakePrefix($word, $edgeLetters)
            . StringHelper::mbTakeSuffix($word, $edgeLetters);
    }
}
