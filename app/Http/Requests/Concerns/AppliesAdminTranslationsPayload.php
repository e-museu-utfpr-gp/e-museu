<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Http\Requests\Contracts\AdminTranslationsPayloadContract;
use Illuminate\Validation\Validator;

/**
 * Admin FormRequests that POST `translations[locale][fields]`: merge this trait and implement
 * {@see self::adminTranslationsPayloadRules()} with a class that implements
 * {@see AdminTranslationsPayloadContract}. Any change to that contract must be reflected in every
 * implementation (item, extra, tag, category, …); add or extend tests when altering behaviour.
 */
trait AppliesAdminTranslationsPayload
{
    /**
     * @return class-string<AdminTranslationsPayloadContract>
     */
    abstract protected function adminTranslationsPayloadRules(): string;

    protected function prepareForValidation(): void
    {
        $raw = $this->input('translations', []);
        if (! is_array($raw)) {
            return;
        }
        /** @var class-string<AdminTranslationsPayloadContract> $class */
        $class = $this->adminTranslationsPayloadRules();
        $this->merge([
            'translations' => $class::normalizeEmptyStringsToNull($raw),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        /** @var class-string<AdminTranslationsPayloadContract> $class */
        $class = $this->adminTranslationsPayloadRules();
        $validator->after(function (Validator $v) use ($class): void {
            $class::validateTranslationConsistency($v, $this->input('translations', []));
        });
    }
}
