<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * Parses assistant "JSON object" replies, including optional markdown fences and trailing noise.
 *
 * General-purpose JSON helper: values may be nested structures. The admin translation pipeline
 * ({@see \App\Actions\Admin\Ai\AdminChatCompletion\Concerns\TranslatesAdminContent}) only keeps
 * scalar string values per catalog field; non-strings are ignored there.
 */
final class ModelJsonContentDecoder
{
    /**
     * @return array<string, mixed>
     */
    public function decodeAssoc(string $raw): array
    {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return [];
        }

        $trimmed = $this->withoutLeadingMarkdownFence($trimmed);

        $decoded = $this->jsonDecodeAssoc($trimmed);
        if ($decoded !== null) {
            return $decoded;
        }

        $slice = $this->firstJsonObjectSlice($trimmed);

        return $this->jsonDecodeAssoc($slice ?? '') ?? [];
    }

    private function withoutLeadingMarkdownFence(string $trimmed): string
    {
        if (! str_starts_with($trimmed, '```')) {
            return $trimmed;
        }

        $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?? $trimmed;
        $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;

        return trim($trimmed);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function jsonDecodeAssoc(string $json): ?array
    {
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        /** @var array<string, mixed> $decoded */

        return $decoded;
    }

    /**
     * Extracts the first top-level JSON object substring using brace depth and string/escape rules,
     * so `{` / `}` inside string values do not truncate the slice. Indices are byte offsets (UTF-8 safe
     * for JSON structural delimiters, which are ASCII).
     */
    private function firstJsonObjectSlice(string $trimmed): ?string
    {
        $start = strpos($trimmed, '{');
        if ($start === false) {
            return null;
        }

        $end = $this->indexOfClosingBraceForTopLevelObject($trimmed, $start);
        if ($end === null) {
            return null;
        }

        return substr($trimmed, $start, $end - $start + 1);
    }

    private function indexOfClosingBraceForTopLevelObject(string $trimmed, int $openBraceIndex): ?int
    {
        $len = strlen($trimmed);
        $depth = 0;
        $inString = false;
        $escape = false;

        for ($i = $openBraceIndex; $i < $len; $i++) {
            $byte = $trimmed[$i];

            if ($inString) {
                [$inString, $escape] = self::stringScannerAdvance($byte, $escape);

                continue;
            }

            if ($byte === '"') {
                $inString = true;

                continue;
            }

            if ($byte === '{') {
                $depth++;

                continue;
            }
            if ($byte === '}') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * @return array{0: bool, 1: bool}  [inString, escapeNext]
     */
    private static function stringScannerAdvance(string $byte, bool $escape): array
    {
        if ($escape) {
            return [true, false];
        }
        if ($byte === '\\') {
            return [true, true];
        }
        if ($byte === '"') {
            return [false, false];
        }

        return [true, false];
    }
}
