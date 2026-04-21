<?php

declare(strict_types=1);

namespace App\Support\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Picks the best matching translation row using {@see ContentLocaleFallback::orderedCodes()}.
 */
final class TranslationResolution
{
    /**
     * @param  Collection<int, Model>  $translations  Models with a `language` BelongsTo (e.g. *Translation).
     */
    public static function fromCollection(Collection $translations): ResolvedTranslation
    {
        $requested = ContentLocaleFallback::normalizedAppLocaleCode();
        if ($translations->isEmpty()) {
            return new ResolvedTranslation(null, null, false);
        }

        $translations->loadMissing('language');

        foreach (ContentLocaleFallback::orderedCodes() as $code) {
            $row = $translations->first(
                function ($t) use ($code): bool {
                    return $t instanceof Model
                        && $t->language !== null
                        && $t->language->code === $code;
                }
            );
            if ($row !== null) {
                return new ResolvedTranslation($row, $code, $code === $requested);
            }
        }

        $deterministic = $translations->sort(function (Model $a, Model $b): int {
            $la = (int) ($a->getAttribute('language_id') ?? 0);
            $lb = (int) ($b->getAttribute('language_id') ?? 0);
            if ($la !== $lb) {
                return $la <=> $lb;
            }

            return ((int) ($a->getAttribute('id') ?? 0)) <=> ((int) ($b->getAttribute('id') ?? 0));
        })->values();

        $first = $deterministic->first();
        if ($first === null) {
            return new ResolvedTranslation(null, null, false);
        }

        $code = $first->language?->code;

        return new ResolvedTranslation($first, $code, $code === $requested);
    }
}
