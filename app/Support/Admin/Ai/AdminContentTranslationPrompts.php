<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * System and user messages for admin catalog AI translation (shared across all OpenAI-compatible providers).
 */
final class AdminContentTranslationPrompts
{
    /**
     * @param  array<string, array<string, string>>  $normalized
     * @param  list<string>  $fields
     */
    public static function documentedSourceBlock(array $normalized, string $targetLocale, array $fields): string
    {
        $lines = [];
        foreach ($fields as $field) {
            $lines[] = "## Field `{$field}`";
            foreach ($normalized as $locale => $row) {
                if ($locale === $targetLocale) {
                    continue;
                }
                $text = trim((string) ($row[$field] ?? ''));
                if ($text === '') {
                    continue;
                }
                $lines[] = "### Locale `{$locale}`";
                $lines[] = $text;
                $lines[] = '';
            }
        }

        return trim(implode("\n", $lines));
    }

    /**
     * @param  list<string>  $applicableFields
     */
    public static function system(
        string $resourceKey,
        string $targetLocale,
        array $applicableFields,
        string $mode,
    ): string {
        $resourceIntro = AdminContentTranslationRegistry::resourceIntroForPrompt($resourceKey);
        $fields = AdminContentTranslationRegistry::fieldsFor($resourceKey);
        $fieldLines = [];
        foreach ($applicableFields as $key) {
            if (! isset($fields[$key])) {
                continue;
            }
            $spec = $fields[$key];
            $desc = (string) $spec['description'];
            $fieldLines[] = "- `{$key}`: {$desc}";
        }
        $fieldList = implode("\n", $fieldLines);
        $keys = '`' . implode('`, `', $applicableFields) . '`';
        $jsonFormatHint = 'Values: JSON strings only (not null). Prefer raw JSON; a leading ```json fence is accepted.';

        $modeHint = $mode === 'regenerate'
            ? 'Mode: overwrite existing target-locale text using the sources.'
            : 'Mode: fill only empty target values; match tone of the sources.';

        $fidelity = implode("\n", [
            'Source fidelity:',
            '- Translate only the labeled source text; keep facts, numbers, names, and scope; '
                . 'no invention or omission beyond normal grammar in the target locale.',
            '- Each JSON value must come only from snippets shown for that field.',
        ]);

        $outputLanguage = implode("\n", [
            'Output language:',
            '- Every JSON string must be entirely in `'
                . $targetLocale
                . '` (visitor-native). If a source is another locale, translate fully; '
                . 'keep codes/identifiers as in sources unless a standard localized form exists.',
        ]);

        return <<<PROMPT
You translate museum catalog content. {$resourceIntro}

Target locale: `{$targetLocale}` — all output strings must read as native for visitors in that language.

{$modeHint}

{$outputLanguage}

{$fidelity}

Reply with one JSON object only, exactly these keys once each: {$keys}.
{$jsonFormatHint}
Do not add other keys.

Fields:
{$fieldList}
PROMPT;
    }

    /**
     * @param  list<string>  $applicableFields
     */
    public static function user(string $targetLocale, array $applicableFields, string $sourceDocument): string
    {
        $fieldsCsv = implode(', ', $applicableFields);

        return <<<PROMPT
Translate into `{$targetLocale}` for fields: {$fieldsCsv}.
Every value must be in that locale, not left in a source language.

The text between the rules below is the authoritative source; do not add commentary.

---
{$sourceDocument}
---
PROMPT;
    }
}
