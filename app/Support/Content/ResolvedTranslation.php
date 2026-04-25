<?php

declare(strict_types=1);

namespace App\Support\Content;

use Illuminate\Database\Eloquent\Model;

/**
 * Result of resolving one row in a `*_translations` collection using {@see TranslationResolution}.
 *
 * @template T of Model
 */
final readonly class ResolvedTranslation
{
    /**
     * @param  T|null  $translation
     */
    public function __construct(
        public ?Model $translation,
        public ?string $sourceLanguageCode,
        public bool $isFromAppLocale,
    ) {
    }

    /**
     * True when a row was found but its language is not the current app locale.
     */
    public function usedFallback(): bool
    {
        return $this->translation !== null && ! $this->isFromAppLocale;
    }

    /**
     * Human label for {@see $sourceLanguageCode} (view.catalog.content_language_names).
     */
    public function sourceLanguageLabel(): ?string
    {
        if ($this->sourceLanguageCode === null) {
            return null;
        }

        $key = 'view.catalog.content_language_names.' . $this->sourceLanguageCode;
        $label = __($key);

        return $label !== $key ? $label : $this->sourceLanguageCode;
    }
}
