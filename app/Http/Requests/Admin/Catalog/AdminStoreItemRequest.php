<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Catalog\CatalogImageRules;
use App\Http\Requests\Concerns\AppliesAdminTranslationsPayload;
use Illuminate\Foundation\Http\FormRequest;

class AdminStoreItemRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(AdminItemTranslationsRules::rules(null), [
            'date' => 'nullable|date',
            'category_id' => 'required|integer|numeric|exists:item_categories,id',
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
            'validation' => 'required|boolean',
        ], CatalogImageRules::requiredCoverAndOptionalGallery());
    }
}
