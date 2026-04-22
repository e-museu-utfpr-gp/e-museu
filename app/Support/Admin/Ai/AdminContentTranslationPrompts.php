<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * System and user messages for admin catalog AI translation (OpenRouter).
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
            $spec = $fields[$key];
            $desc = (string) $spec['description'];
            $fieldLines[] = "- `{$key}`: {$desc}";
        }
        $fieldList = implode("\n", $fieldLines);
        $keys = '`' . implode('`, `', $applicableFields) . '`';

        $modeHint = $mode === 'regenerate'
            ? 'Overwrite prior text in the target locale with fresh translations.'
            : 'Only provide translations for missing target values; keep tone consistent with sources.';

        $fidelity = implode("\n", [
            'Source fidelity (mandatory):',
            '- The labeled snippets are the exact source strings to translate.',
            '- Preserve facts, scope, numbers, names, and intent from those snippets only.',
            '- Do not invent, summarize, expand, or omit substantive content except for normal grammar '
            . 'in the target locale.',
            '- Each JSON value must derive only from sources shown for that field (no outside content).',
        ]);

        return <<<PROMPT
You are a professional translator and editor for a museum catalog application.
{$resourceIntro}

Target locale code: `{$targetLocale}` (produce natural text for visitors in that language).

{$modeHint}

{$fidelity}

Return ONLY a single JSON object with these keys exactly once each: {$keys}.
Values must be JSON strings (not null). Do not wrap the JSON in markdown fences.
Do not add keys outside the list.

Field meanings:
{$fieldList}
PROMPT;
    }

    /**
     * @param  list<string>  $applicableFields
     */
    public static function user(string $targetLocale, array $applicableFields, string $sourceDocument): string
    {
        $fieldsCsv = implode(', ', $applicableFields);
        $exactCopy = implode("\n", [
            'The text between the horizontal rules is the exact catalog copy you must translate.',
            'Do not substitute other wording, hypotheticals, or commentary.',
        ]);

        return <<<PROMPT
Translate the following labeled source snippets into locale `{$targetLocale}` for fields: {$fieldsCsv}.

{$exactCopy}

Source snippets (each section states the field name and source locale):
---
{$sourceDocument}
---
PROMPT;
    }
}
