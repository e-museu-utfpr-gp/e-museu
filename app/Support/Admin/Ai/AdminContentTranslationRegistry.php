<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

use InvalidArgumentException;

/**
 * Catalog admin entities that share the same `translations[locale][field]` form shape.
 *
 * @phpstan-type FieldSpec array{max:int, description:string}
 */
final class AdminContentTranslationRegistry
{
    public const RESOURCE_ITEM = 'item';

    public const RESOURCE_EXTRA = 'extra';

    public const RESOURCE_ITEM_CATEGORY = 'item_category';

    public const RESOURCE_TAG = 'tag';

    public const RESOURCE_TAG_CATEGORY = 'tag_category';

    /**
     * @return list<string>
     */
    public static function resourceKeys(): array
    {
        return [
            self::RESOURCE_ITEM,
            self::RESOURCE_EXTRA,
            self::RESOURCE_ITEM_CATEGORY,
            self::RESOURCE_TAG,
            self::RESOURCE_TAG_CATEGORY,
        ];
    }

    /**
     * @return array<string, FieldSpec>
     */
    public static function fieldsFor(string $resourceKey): array
    {
        return match ($resourceKey) {
            self::RESOURCE_ITEM => [
                'name' => [
                    'max' => 200,
                    'description' => 'Short public title of the catalog item.',
                ],
                'description' => [
                    'max' => 1000,
                    'description' => 'Brief summary shown in listings and previews.',
                ],
                'detail' => [
                    'max' => 10000,
                    'description' => 'Full descriptive text for the item page.',
                ],
                'history' => [
                    'max' => 100000,
                    'description' => 'Long-form historical narrative; may use paragraphs.',
                ],
            ],
            self::RESOURCE_EXTRA => [
                'info' => [
                    'max' => 10000,
                    'description' => 'Supplementary information about the item (extra block).',
                ],
            ],
            self::RESOURCE_ITEM_CATEGORY,
            self::RESOURCE_TAG,
            self::RESOURCE_TAG_CATEGORY => [
                'name' => [
                    'max' => 200,
                    'description' => 'Short display name for taxonomy or navigation.',
                ],
            ],
            default => throw new InvalidArgumentException("Unknown translation resource: {$resourceKey}"),
        };
    }

    /**
     * @return list<string>
     */
    public static function fieldKeysFor(string $resourceKey): array
    {
        return array_keys(self::fieldsFor($resourceKey));
    }

    public static function resourceIntroForPrompt(string $resourceKey): string
    {
        return match ($resourceKey) {
            self::RESOURCE_ITEM => 'Museum catalog item (public-facing metadata).',
            self::RESOURCE_EXTRA => 'Museum catalog extra note attached to an item.',
            self::RESOURCE_ITEM_CATEGORY => 'Museum item category label.',
            self::RESOURCE_TAG => 'Museum taxonomy tag label.',
            self::RESOURCE_TAG_CATEGORY => 'Museum taxonomy tag group label.',
            default => 'Museum catalog content.',
        };
    }
}
