<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

final class AdminAiViewData
{
    /**
     * @return array{
     *     aiTranslationUiEnabled: bool,
     *     aiTranslationResource: string|null,
     *     aiTranslationFieldKeys: string
     * }
     */
    public static function forTranslationResource(string $resourceKey): array
    {
        if (! AdminAi::translationUiEnabled()) {
            return [
                'aiTranslationUiEnabled' => false,
                'aiTranslationResource' => null,
                'aiTranslationFieldKeys' => '',
            ];
        }

        return [
            'aiTranslationUiEnabled' => true,
            'aiTranslationResource' => $resourceKey,
            'aiTranslationFieldKeys' => implode(',', AdminContentTranslationRegistry::fieldKeysFor($resourceKey)),
        ];
    }
}
