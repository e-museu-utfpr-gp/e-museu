<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\Collaborator\CollaboratorRequest;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SingleExtraRequest extends FormRequest
{
    /**
     * Collaborator fields and extra fields are validated in one pass (no duplicate validation in
     * {@see ItemContributionValidator::validateSingleExtra()}).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge((new CollaboratorRequest())->rules(), [
            'content_locale' => [
                'required',
                'string',
                Rule::in(once(fn (): array => Language::forCatalogContentForms()->pluck('code')->all())),
            ],
            'info' => 'required|string|min:1|max:10000',
            'item_id' => 'required|integer|numeric|exists:items,id',
            'collaborator_id' => 'sometimes|integer|numeric|exists:collaborators,id',
        ]);
    }
}
