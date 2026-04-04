<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\AppliesAdminTranslationsPayload;
use App\Models\Catalog\ItemCategory;
use Illuminate\Foundation\Http\FormRequest;

class AdminItemCategoryRequest extends FormRequest
{
    use AppliesAdminTranslationsPayload;

    /**
     * @return class-string<\App\Http\Requests\Contracts\AdminTranslationsPayloadContract>
     */
    protected function adminTranslationsPayloadRules(): string
    {
        return AdminItemCategoryTranslationsRules::class;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('item_category');

        return AdminItemCategoryTranslationsRules::rules($category instanceof ItemCategory ? $category : null);
    }
}
