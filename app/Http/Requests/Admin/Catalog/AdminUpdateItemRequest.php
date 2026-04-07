<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Catalog\CatalogImageRules;
use App\Http\Requests\Concerns\AppliesAdminTranslationsPayload;
use App\Models\Catalog\Item;
use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateItemRequest extends FormRequest
{
    use AppliesAdminTranslationsPayload;

    /**
     * @return class-string<\App\Http\Requests\Contracts\AdminTranslationsPayloadContract>
     */
    protected function adminTranslationsPayloadRules(): string
    {
        return AdminItemTranslationsRules::class;
    }

    /**
     * @return array<string, string|array<int, string|\Illuminate\Validation\Rules\Unique>>
     */
    public function rules(): array
    {
        $route = $this->route();
        $item = $route ? $route->parameter('item') : null;

        return array_merge(AdminItemTranslationsRules::rules($item instanceof Item ? $item : null), [
            'date' => 'nullable|date',
            'category_id' => 'required|integer|numeric|exists:item_categories,id',
            'location_id' => 'required|integer|numeric|exists:locations,id',
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
            'identification_code' => 'required|string|min:1|max:50',
            'validation' => 'required|boolean',
        ], CatalogImageRules::adminItemUpdate());
    }
}
