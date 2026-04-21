<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Catalog;

use Closure;

/**
 * Validates that an attached catalog id (tag, component item) is not the same as `item_id`.
 */
final class CatalogRelationIdRules
{
    /**
     * @return list<string|Closure>
     */
    public static function tagIdMustDifferFromItem(): array
    {
        return [
            'required',
            'integer',
            'numeric',
            'exists:tags,id',
            function (string $attribute, mixed $value, Closure $fail): void {
                if ((string) request('item_id') === (string) $value) {
                    $fail(__('validation.catalog.item_tag_different'));
                }
            },
        ];
    }

    /**
     * @return list<string|Closure>
     */
    public static function componentItemIdMustDifferFromParentItem(): array
    {
        return [
            'sometimes',
            'integer',
            'numeric',
            'exists:items,id',
            function (string $attribute, mixed $value, Closure $fail): void {
                if ((string) request('item_id') === (string) $value) {
                    $fail(__('validation.catalog.item_component_different'));
                }
            },
        ];
    }
}
