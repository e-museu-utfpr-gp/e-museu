<?php

namespace Tests\Feature\Admin\Catalog;

use App\Http\Requests\Admin\Catalog\AdminItemTranslationsRules;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Covers {@see \App\Http\Requests\Concerns\AppliesAdminTranslationsPayload} behavior for item
 * translations (empty-string normalization + cross-locale consistency).
 */
#[Group('mysql')]
class AdminItemTranslationsPayloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped(
                'Admin translation tests require pdo_mysql (run in the app Docker container).'
            );
        }

        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Set DB_CONNECTION=mysql in .env.testing.');
        }
    }

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
}
