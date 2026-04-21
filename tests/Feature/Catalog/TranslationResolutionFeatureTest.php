<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Catalog\{Item, ItemCategory, ItemTranslation};
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;

/**
 * Catalog translation resolution uses MySQL-specific SQL; the project targets MySQL.
 * Use DB_CONNECTION=mysql in testing.
 */
#[Group('mysql')]
class TranslationResolutionFeatureTest extends AbstractMysqlRefreshDatabaseTestCase
{
    use RequiresMysqlDriverConnection;

    private function anySeededLocationId(): int
    {
        $id = Location::query()->orderBy('id')->value('id');
        $this->assertNotNull($id, 'Migration seed must create at least one location row.');

        return (int) $id;
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
            'location_id' => $this->anySeededLocationId(),
        ]);
        $ptId = (int) Language::query()->where('code', 'pt_BR')->value('id');
        $enId = (int) Language::query()->where('code', 'en')->value('id');

        ItemTranslation::query()->where('item_id', $item->id)->delete();

        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $ptId,
            'name' => 'Name PT',
            'description' => 'Description PT',
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
        $this->assertSame('Name PT', $freshPt->resolveTranslation()->translation?->name);
        $this->assertTrue($freshPt->resolveTranslation()->isFromAppLocale);

        app()->setLocale('en');
        $freshEn = Item::query()->with('translations.language')->findOrFail($item->id);
        $this->assertSame('English name', $freshEn->resolveTranslation()->translation?->name);
        $this->assertTrue($freshEn->resolveTranslation()->isFromAppLocale);
    }

    public function test_resolve_translation_is_null_when_no_translation_rows(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Cat']);

        $collaborator = Collaborator::factory()->create();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => 'TEST_RES_EMPTY',
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $this->anySeededLocationId(),
        ]);
        ItemTranslation::query()->where('item_id', $item->id)->delete();

        $fresh = Item::query()->with('translations.language')->findOrFail($item->id);
        $resolved = $fresh->resolveTranslation();
        $this->assertNull($resolved->translation);
        $this->assertFalse($resolved->isFromAppLocale);
    }

    public function test_resolve_translation_falls_back_to_english_when_pt_missing(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Cat']);

        $collaborator = Collaborator::factory()->create();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => 'TEST_RES_FB_EN',
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $this->anySeededLocationId(),
        ]);
        ItemTranslation::query()->where('item_id', $item->id)->delete();

        $enId = (int) Language::query()->where('code', 'en')->value('id');
        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $enId,
            'name' => 'English only',
            'description' => 'Desc EN',
            'history' => null,
            'detail' => null,
        ]);

        $fresh = Item::query()->with('translations.language')->findOrFail($item->id);
        $resolved = $fresh->resolveTranslation();
        $this->assertSame('English only', $resolved->translation?->name);
        $this->assertTrue($resolved->usedFallback());
    }

    public function test_resolve_translation_uses_universal_when_pt_and_en_missing(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Cat']);

        $collaborator = Collaborator::factory()->create();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => 'TEST_RES_NEUT',
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $this->anySeededLocationId(),
        ]);
        ItemTranslation::query()->where('item_id', $item->id)->delete();

        $universalId = (int) Language::query()->where('code', 'universal')->value('id');
        $this->assertGreaterThan(0, $universalId);

        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $universalId,
            'name' => 'Universal name',
            'description' => 'Universal desc',
            'history' => null,
            'detail' => null,
        ]);

        $fresh = Item::query()->with('translations.language')->findOrFail($item->id);
        $resolved = $fresh->resolveTranslation();
        $this->assertSame('Universal name', $resolved->translation?->name);
        $this->assertSame('universal', $resolved->sourceLanguageCode);
        $this->assertTrue($resolved->usedFallback());
    }
}
