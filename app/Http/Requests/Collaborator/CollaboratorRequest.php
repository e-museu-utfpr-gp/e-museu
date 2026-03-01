<?php

namespace App\Http\Requests\Collaborator;

use App\Enums\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Foundation\Http\FormRequest;

class CollaboratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules for public contribution (external users). Contact must not belong to an INTERNAL collaborator.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:1|max:200',
            'contact' => [
                'required',
                'email:rfc,dns',
                'min:1',
                'max:200',
                'unique:collaborators,contact',
                function (string $attribute, string $value, \Closure $fail): void {
                    if (Collaborator::where('contact', $value)->where('role', CollaboratorRole::INTERNAL)->exists()) {
                        $fail(__('app.collaborator.contact_reserved_for_internal'));
                    }
                },
            ],
        ];
    }
}
