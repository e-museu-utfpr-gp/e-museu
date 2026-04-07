<?php

namespace App\Http\Requests\Admin\Taxonomy;

use App\Http\Requests\Concerns\AppliesAdminTranslationsPayload;
use App\Models\Taxonomy\Tag;
use Illuminate\Foundation\Http\FormRequest;

class AdminTagRequest extends FormRequest
{
    use AppliesAdminTranslationsPayload;

    /**
     * @return class-string<\App\Http\Requests\Contracts\AdminTranslationsPayloadContract>
     */
    protected function adminTranslationsPayloadRules(): string
    {
        return AdminTagTranslationsRules::class;
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
