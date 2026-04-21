<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Http\Requests\Collaborator\PublicCollaboratorRules;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
        return array_merge(PublicCollaboratorRules::rules(), [
            'content_locale' => [
                'required',
                'string',
                Rule::in(once(fn (): array => Language::forCatalogContentForms()->pluck('code')->all())),
            ],
            'info' => 'required|string|min:1|max:10000',
            'item_id' => 'required|integer|numeric|exists:items,id',
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $id = $this->input('collaborator_id');
            $email = trim((string) $this->input('email', ''));
            if (! is_numeric($id) || $email === '') {
                return;
            }

            $collaborator = Collaborator::query()->find((int) $id);
            if ($collaborator === null) {
                return;
            }

            if ($collaborator->role !== CollaboratorRole::EXTERNAL || $collaborator->blocked) {
                $v->errors()->add('collaborator_id', __('app.collaborator.blocked_from_registering'));

                return;
            }

            if (! hash_equals(mb_strtolower(trim((string) $collaborator->email)), mb_strtolower($email))) {
                $v->errors()->add('email', __('app.collaborator.extra_collaborator_email_mismatch'));
            }
        });
    }
}
