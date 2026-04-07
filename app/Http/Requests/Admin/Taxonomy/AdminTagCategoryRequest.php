<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Http\Requests\Concerns\AppliesAdminTranslationsPayload;
use App\Models\Taxonomy\TagCategory;
use Illuminate\Foundation\Http\FormRequest;

class AdminTagCategoryRequest extends FormRequest
{
    use AppliesAdminTranslationsPayload;

    /**
     * @return class-string<\App\Http\Requests\Contracts\AdminTranslationsPayloadContract>
     */
    protected function adminTranslationsPayloadRules(): string
    {
        return AdminTagCategoryTranslationsRules::class;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('tag_category');

        return AdminTagCategoryTranslationsRules::rules($category instanceof TagCategory ? $category : null);
    }
}
