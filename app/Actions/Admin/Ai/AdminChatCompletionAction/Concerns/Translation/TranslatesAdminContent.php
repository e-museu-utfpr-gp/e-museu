<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Translation;

use App\Enums\Content\ContentLanguage;
use App\Exceptions\AiTranslationUserException;
use App\Models\Language;
use App\Support\Admin\Ai\AdminContentTranslationPrompts;
use App\Support\Admin\Ai\AdminContentTranslationRegistry;

/**
 * Admin resource translation: prompts, provider-chain chat completion, model JSON validation.
 */
trait TranslatesAdminContent
{
    /**
     * @param  array<string, mixed>  $translationsByLocale
     * @return array<string, string>
     */
    public function translateContent(
        string $resourceKey,
        string $targetLocale,
        string $mode,
        array $translationsByLocale,
    ): array {
        if ($targetLocale === ContentLanguage::UNIVERSAL->value) {
            throw new AiTranslationUserException('view.admin.ai.error_universal_target');
        }

        $fields = AdminContentTranslationRegistry::fieldsFor($resourceKey);
        $normalized = $this->normalizeIncomingTranslations($translationsByLocale, array_keys($fields));

        $fieldsWithSource = $this->fieldsWithCrossLocaleSource($normalized, $targetLocale, array_keys($fields));
        if ($fieldsWithSource === []) {
            throw new AiTranslationUserException('view.admin.ai.error_no_source');
        }

        $targetSnapshot = $normalized[$targetLocale] ?? [];
        $applicableFields = $this->fieldsApplicableForMode(
            $fieldsWithSource,
            $targetSnapshot,
            $mode,
        );

        if ($applicableFields === []) {
            throw new AiTranslationUserException('view.admin.ai.error_no_applicable');
        }

        $sourceDocument = AdminContentTranslationPrompts::documentedSourceBlock(
            $normalized,
            $targetLocale,
            $applicableFields,
        );

        $maxSource = (int) config('ai.translation.max_source_chars');
        if (strlen($sourceDocument) > $maxSource) {
            throw new AiTranslationUserException('view.admin.ai.error_payload_too_large');
        }

        $system = AdminContentTranslationPrompts::system($resourceKey, $targetLocale, $applicableFields, $mode);
        $user = AdminContentTranslationPrompts::user($targetLocale, $applicableFields, $sourceDocument);

        /** @var list<string> $models */
        $models = config('ai.openrouter.models', []);
        $result = $this->handle($system, $user, $models);

        $parsed = $this->modelJsonDecoder->decodeAssoc($result['content']);
        $out = $this->validateAndClampOutput($parsed, $applicableFields, $fields);

        if ($out === []) {
            throw new AiTranslationUserException('view.admin.ai.error_model_empty');
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @param  list<string>  $fieldKeys
     * @return array<string, array<string, string>>
     */
    private function normalizeIncomingTranslations(array $raw, array $fieldKeys): array
    {
        $allowedLocales = [];
        foreach (Language::forCatalogContentForms() as $lang) {
            $allowedLocales[$lang->code] = true;
        }

        $out = [];
        foreach ($raw as $locale => $block) {
            if (! isset($allowedLocales[$locale]) || ! is_array($block)) {
                continue;
            }
            /** @var array<string, mixed> $block */
            $row = [];
            foreach ($fieldKeys as $field) {
                if (! array_key_exists($field, $block)) {
                    continue;
                }
                $value = $block[$field];
                $row[$field] = is_string($value) ? $value : '';
            }
            $out[$locale] = $row;
        }

        return $out;
    }

    /**
     * @param  array<string, array<string, string>>  $normalized
     * @param  list<string>  $fieldKeys
     * @return list<string>
     */
    private function fieldsWithCrossLocaleSource(array $normalized, string $targetLocale, array $fieldKeys): array
    {
        $with = [];
        foreach ($fieldKeys as $field) {
            foreach ($normalized as $locale => $row) {
                if ($locale === $targetLocale) {
                    continue;
                }
                $chunk = trim((string) ($row[$field] ?? ''));
                if ($chunk !== '') {
                    $with[$field] = true;

                    break;
                }
            }
        }

        return array_keys($with);
    }

    /**
     * @param  list<string>  $fieldsWithSource
     * @param  array<string, string>  $targetSnapshot
     * @return list<string>
     */
    private function fieldsApplicableForMode(array $fieldsWithSource, array $targetSnapshot, string $mode): array
    {
        $out = [];
        foreach ($fieldsWithSource as $field) {
            $targetVal = trim((string) ($targetSnapshot[$field] ?? ''));
            if ($mode === 'regenerate') {
                if ($targetVal !== '') {
                    $out[] = $field;
                }

                continue;
            }

            if ($targetVal === '') {
                $out[] = $field;
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @param  list<string>  $allowedKeys
     * @param  array<string, array{max:int, description:string}>  $fieldSpecs
     * @return array<string, string>
     */
    private function validateAndClampOutput(array $parsed, array $allowedKeys, array $fieldSpecs): array
    {
        $out = [];
        foreach ($allowedKeys as $key) {
            if (! array_key_exists($key, $parsed)) {
                continue;
            }
            $value = $parsed[$key];
            if (! is_string($value)) {
                continue;
            }
            $trimmed = trim($value);
            if ($trimmed === '') {
                continue;
            }
            $max = (int) ($fieldSpecs[$key]['max'] ?? 65535);
            if (mb_strlen($trimmed) > $max) {
                $trimmed = mb_substr($trimmed, 0, $max);
            }
            $out[$key] = $trimmed;
        }

        return $out;
    }
}
