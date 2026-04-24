<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

final class AdminAiViewData
{
    /**
     * @return array{
     *     aiTranslationUiEnabled: bool,
     *     aiTranslationResource: string|null,
     *     aiTranslationFieldKeys: string,
     *     aiTranslationFieldLimits: array<string, int>,
     *     aiTranslationProviders: list<string>,
     *     aiTranslationProviderOptions: array<int, array{value: string, label: string}>
     * }
     */
    public static function forTranslationResource(string $resourceKey): array
    {
        if (! AdminAi::translationUiEnabled()) {
            return [
                'aiTranslationUiEnabled' => false,
                'aiTranslationResource' => null,
                'aiTranslationFieldKeys' => '',
                'aiTranslationFieldLimits' => [],
                'aiTranslationProviders' => [],
                'aiTranslationProviderOptions' => [],
            ];
        }

        $providerOptions = [];
        foreach (AdminAi::configuredProviderSlugs() as $slug) {
            $providerOptions[] = [
                'value' => $slug,
                'label' => AdminAi::providerLabel($slug),
            ];
        }

        $fieldLimits = [];
        foreach (AdminContentTranslationRegistry::fieldsFor($resourceKey) as $fieldName => $spec) {
            $fieldLimits[$fieldName] = (int) $spec['max'];
        }

        return [
            'aiTranslationUiEnabled' => true,
            'aiTranslationResource' => $resourceKey,
            'aiTranslationFieldKeys' => implode(',', AdminContentTranslationRegistry::fieldKeysFor($resourceKey)),
            'aiTranslationFieldLimits' => $fieldLimits,
            'aiTranslationProviders' => AdminAi::configuredProviderSlugs(),
            'aiTranslationProviderOptions' => $providerOptions,
        ];
    }
}
