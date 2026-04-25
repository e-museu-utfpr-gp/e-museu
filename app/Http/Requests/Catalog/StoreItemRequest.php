<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    /**
     * Public item contribution: cover_image is the required main image (stored as type "cover");
     * gallery_images is an optional array of extra images (stored as type "gallery"). Max 10MB per file.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'content_locale' => [
                'required',
                'string',
                Rule::in(once(fn (): array => Language::forCatalogContentForms()->pluck('code')->all())),
            ],
            'name' => [
                'required',
                'string',
                'min:1',
                'max:200',
            ],
            'date' => 'nullable|date',
            'description' => 'required|string|min:1|max:1000',
            'detail' => 'nullable|string|max:10000',
            'history' => 'nullable|string|max:100000',
            'category_id' => 'required|integer|numeric|exists:item_categories,id',
            'location_id' => 'required|integer|numeric|exists:locations,id',
        ], CatalogImageRules::requiredCoverAndOptionalGallery());
    }
}
