<?php

namespace App\Models\Concerns;

use App\Models\Language;
use App\Support\Content\ResolvedTranslation;
use App\Support\Content\TranslationResolution;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Shared {@see syncTranslationsFromAdminForm()} / resolution for models whose only translatable
 * field in admin is `name` (item categories, tag categories, tags).
 *
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Model
 */
trait SyncsAdminFormNameTranslations
{
    /**
     * @param  array{name: string}  $fields
     */
    public function syncPrimaryLocaleTranslation(array $fields): void
    {
        $this->syncTranslationForLanguage(Language::idForPreferredFormLocale(), $fields);
    }

    /**
     * @param  array{name: string}  $fields
     */
    public function syncTranslationForLanguage(int $languageId, array $fields): void
    {
        $this->translations()->updateOrCreate(
            ['language_id' => $languageId],
            $fields
        );
    }

    /**
     * @param  array<string, array<string, mixed>|null>  $translationsByCode
     */
    public function syncTranslationsFromAdminForm(array $translationsByCode): void
    {
        foreach (Language::forAdminContentForms() as $lang) {
            $code = $lang->code;
            $block = $translationsByCode[$code] ?? [];
            $name = trim((string) ($block['name'] ?? ''));
            if ($name === '') {
                $this->translations()->where('language_id', $lang->id)->delete();

                continue;
            }

            $this->translations()->updateOrCreate(
                ['language_id' => $lang->id],
                ['name' => $name]
            );
        }

        if ($this->relationLoaded('translations')) {
            $this->unsetRelation('translations');
        }
    }

    public function resolveTranslation(): ResolvedTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return TranslationResolution::fromCollection($this->translations);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::get(function (): string {
            if (array_key_exists('name', $this->attributes) && $this->attributes['name'] !== null) {
                return (string) $this->attributes['name'];
            }

            return (string) ($this->resolvedTranslation()?->name ?? '');
        });
    }
}
