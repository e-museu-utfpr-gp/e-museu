<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Ai;

use App\Enums\Content\ContentLanguage;
use App\Models\Language;
use App\Support\Admin\Ai\AdminAi;
use App\Support\Admin\Ai\AdminContentTranslationRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AdminContentTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $resourceKeys = AdminContentTranslationRegistry::resourceKeys();

        $rules = [
            'resource' => ['required', 'string', Rule::in($resourceKeys)],
            'target_locale' => [
                'required',
                'string',
                Rule::in(Language::forCatalogContentForms()->pluck('code')->all()),
                Rule::notIn([ContentLanguage::UNIVERSAL->value]),
            ],
            'mode' => ['required', 'string', Rule::in(['fill', 'regenerate'])],
            'provider' => ['nullable', 'string', Rule::in(array_merge(['auto'], AdminAi::allProviderSlugs()))],
            'translations' => ['required', 'array'],
        ];

        foreach (Language::forCatalogContentForms() as $lang) {
            $c = $lang->code;
            $rules["translations.{$c}"] = ['nullable', 'array'];
        }

        $resource = $this->input('resource');
        if (is_string($resource) && in_array($resource, $resourceKeys, true)) {
            $fields = AdminContentTranslationRegistry::fieldsFor($resource);
            foreach (Language::forCatalogContentForms() as $lang) {
                $c = $lang->code;
                foreach ($fields as $fieldName => $spec) {
                    $max = (int) $spec['max'];
                    $rules["translations.{$c}.{$fieldName}"] = ['nullable', 'string', 'max:' . $max];
                }
            }
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedTranslationsPayload(): array
    {
        /** @var array<string, mixed> $data */
        $data = $this->validated();

        return is_array($data['translations'] ?? null) ? $data['translations'] : [];
    }
}
