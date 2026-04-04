<?php

namespace App\Http\Requests\Taxonomy;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
        return [
            'tags' => [
                'sometimes',
                'array',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_array($value)) {
                        return;
                    }
                    $seen = [];
                    foreach ($value as $row) {
                        if (! is_array($row)) {
                            continue;
                        }
                        $cid = (string) ($row['category_id'] ?? '');
                        $name = mb_strtolower(trim((string) ($row['name'] ?? '')));
                        if ($name === '') {
                            continue;
                        }
                        $key = $cid . "\0" . $name;
                        if (isset($seen[$key])) {
                            $fail(__('validation.catalog.tags_duplicate_in_request'));

                            return;
                        }
                        $seen[$key] = true;
                    }
                },
            ],
            'tags.*.category_id' => 'sometimes|required|integer|numeric|exists:tag_categories,id',
            'tags.*.name' => 'sometimes|required|string|min:1|max:200',
        ];
    }
}
