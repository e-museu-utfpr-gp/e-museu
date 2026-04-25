<?php

declare(strict_types=1);

namespace App\Support\Content;

/**
 * Translation column keys per parent entity (for `split()`), and helper to separate them from parent attributes.
 */
final class TranslatablePayload
{
    /** @var list<string> Keys for `item_translations`. */
    public const ITEM_KEYS = ['name', 'description', 'history', 'detail'];

    /** @var list<string> Keys for `item_category_translations`. */
    public const ITEM_CATEGORY_KEYS = ['name'];

    /** @var list<string> Keys for `extra_translations`. */
    public const EXTRA_KEYS = ['info'];

    /** @var list<string> Keys for `tag_translations`. */
    public const TAG_KEYS = ['name'];

    /** @var list<string> Keys for `tag_category_translations`. */
    public const TAG_CATEGORY_KEYS = ['name'];

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $translationKeys  Keys that belong in a `*_translations` row, not the parent table.
     * @return array{translation: array<string, mixed>, persist: array<string, mixed>}
     */
    public static function split(array $data, array $translationKeys): array
    {
        $flip = array_flip($translationKeys);

        return [
            'translation' => array_intersect_key($data, $flip),
            'persist' => array_diff_key($data, $flip),
        ];
    }
}
