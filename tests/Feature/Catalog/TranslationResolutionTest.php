<?php

namespace Tests\Feature\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Catalog\ItemTranslation;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Catalog translation resolution uses MySQL-specific SQL; the project targets MySQL.
 * Use DB_CONNECTION=mysql in testing.
 */
#[Group('mysql')]
class TranslationResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped(
                'Catalog tests require pdo_mysql (install the extension or run tests in the app Docker container).'
            );
        }

        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped(
                'Set DB_CONNECTION=mysql in .env.testing (translation queries assume MySQL).'
            );
        }
    }

    public function test_item_resolve_translation_prefers_current_locale_when_present(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Test category']);

        $collaborator = Collaborator::factory()->create();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => 'TEST_RES_1',
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
        ]);
        $ptId = (int) Language::query()->where('code', 'pt_BR')->value('id');
        $enId = (int) Language::query()->where('code', 'en')->value('id');

        ItemTranslation::query()->where('item_id', $item->id)->delete();

        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $ptId,
            'name' => 'Nome PT',
            'description' => 'Descrição',
            'history' => null,
            'detail' => null,
        ]);
        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $enId,
            'name' => 'English name',
            'description' => 'Description',
            'history' => null,
            'detail' => null,
        ]);

        $freshPt = Item::query()->with('translations.language')->findOrFail($item->id);
        $this->assertSame('Nome PT', $freshPt->resolveTranslation()->translation?->name);
        $this->assertTrue($freshPt->resolveTranslation()->isFromAppLocale);

        app()->setLocale('en');
        $freshEn = Item::query()->with('translations.language')->findOrFail($item->id);
        $this->assertSame('English name', $freshEn->resolveTranslation()->translation?->name);
        $this->assertTrue($freshEn->resolveTranslation()->isFromAppLocale);
    }
}
