<?php

namespace App\Http\Requests\Catalog;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SingleExtraRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $languageCodes = once(fn (): array => Language::query()->pluck('code')->all());

        return [
            'content_locale' => ['required', 'string', Rule::in($languageCodes)],
            'info' => 'required|string|min:1|max:10000',
            'item_id' => 'required|integer|numeric|exists:items,id',
            'collaborator_id' => 'sometimes|integer|numeric|exists:collaborators,id',
            'validation' => 'sometimes|boolean',
        ];
    }
}
