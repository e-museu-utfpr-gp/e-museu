<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Public item contribution: cover_image is the required main image (stored as type "cover");
     * gallery_images is an optional array of extra images (stored as type "gallery"). Max 10MB per file.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:200|unique:items',
            'date' => 'nullable|date',
            'description' => 'required|string|min:1|max:1000',
            'detail' => 'nullable|max:10000',
            'history' => 'nullable|max:100000',
            'category_id' => 'required|integer|numeric|exists:item_categories,id',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery_images' => 'sometimes|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }
}
