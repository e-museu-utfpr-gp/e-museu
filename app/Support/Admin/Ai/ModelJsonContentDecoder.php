<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * Parses assistant "JSON object" replies, including optional markdown fences and trailing noise.
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

    private function firstJsonObjectSlice(string $trimmed): ?string
    {
        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        return substr($trimmed, $start, $end - $start + 1);
    }
}
