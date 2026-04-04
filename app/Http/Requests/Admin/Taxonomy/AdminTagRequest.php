<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Models\Taxonomy\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdminTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('translations', []);
        if (! is_array($raw)) {
            return;
        }
        $this->merge([
            'translations' => AdminTagTranslationsRules::normalizeEmptyStringsToNull($raw),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            AdminTagTranslationsRules::validateTranslationConsistency(
                $validator,
                $this->input('translations', [])
            );
        });
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tag = $this->route('tag');

        return AdminTagTranslationsRules::rules(
            $tag instanceof Tag ? $tag : null,
            $this->filled('category_id') ? (int) $this->input('category_id') : null
        );
    }
}
