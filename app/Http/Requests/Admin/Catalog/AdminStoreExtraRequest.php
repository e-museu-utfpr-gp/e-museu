<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Catalog;

use App\Http\Requests\Concerns\AppliesAdminTranslationsPayload;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin CRUD for extras — multi-locale `info` like other catalog admin forms.
 */
class AdminStoreExtraRequest extends FormRequest
{
    use AppliesAdminTranslationsPayload;

    /**
     * @return class-string<\App\Http\Requests\Contracts\AdminTranslationsPayloadContract>
     */
    protected function adminTranslationsPayloadRules(): string
    {
        return AdminExtraTranslationsRules::class;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return AdminExtraTranslationsRules::rules();
    }
}
