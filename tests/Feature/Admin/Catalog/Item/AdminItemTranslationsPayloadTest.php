<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog\Item;

use App\Http\Requests\Admin\Catalog\AdminItemTranslationsRules;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;

/**
 * Covers {@see \App\Http\Requests\Concerns\AppliesAdminTranslationsPayload} behavior for item
 * translations (empty-string normalization + cross-locale consistency).
 */
#[Group('mysql')]
class AdminItemTranslationsPayloadTest extends AbstractMysqlRefreshDatabaseTestCase
{
    use RequiresMysqlDriverConnection;

    public function test_normalize_empty_strings_to_null_for_known_locales(): void
    {
        $raw = [
            'pt_BR' => [
                'name' => '',
                'description' => 'Filled',
                'detail' => '',
                'history' => '',
            ],
        ];

        $out = AdminItemTranslationsRules::normalizeEmptyStringsToNull($raw);

        $this->assertNull($out['pt_BR']['name']);
        $this->assertSame('Filled', $out['pt_BR']['description']);
        $this->assertNull($out['pt_BR']['detail']);
        $this->assertNull($out['pt_BR']['history']);
    }

    public function test_validate_translation_consistency_errors_when_no_locale_complete(): void
    {
        $data = [
            'translations' => [
                'pt_BR' => ['name' => '', 'description' => ''],
                'en' => ['name' => '', 'description' => ''],
            ],
        ];

        $validator = Validator::make($data, AdminItemTranslationsRules::rules());
        AdminItemTranslationsRules::validateTranslationConsistency($validator, $data['translations']);

        $this->assertTrue($validator->errors()->has('translations'));
    }

    public function test_validate_translation_consistency_passes_when_one_locale_complete(): void
    {
        $data = [
            'translations' => [
                'pt_BR' => ['name' => 'N', 'description' => 'D'],
                'en' => ['name' => '', 'description' => ''],
            ],
        ];

        $validator = Validator::make($data, AdminItemTranslationsRules::rules());
        AdminItemTranslationsRules::validateTranslationConsistency($validator, $data['translations']);

        $this->assertFalse($validator->errors()->has('translations'));
    }

    public function test_validate_translation_consistency_errors_when_locale_partial_without_name(): void
    {
        $data = [
            'translations' => [
                'pt_BR' => [
                    'name' => '',
                    'description' => 'Description only',
                    'detail' => '',
                    'history' => '',
                ],
                'en' => ['name' => '', 'description' => '', 'detail' => '', 'history' => ''],
            ],
        ];

        $validator = Validator::make($data, AdminItemTranslationsRules::rules());
        AdminItemTranslationsRules::validateTranslationConsistency($validator, $data['translations']);

        $this->assertTrue($validator->errors()->has('translations.pt_BR.description'));
    }
}
