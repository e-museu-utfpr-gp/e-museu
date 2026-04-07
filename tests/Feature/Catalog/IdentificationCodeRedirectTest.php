<?php

namespace Tests\Feature\Catalog;

use App\Models\Catalog\{Item, ItemCategory};
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use App\Support\Catalog\ItemIdentificationCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('mysql')]
final class IdentificationCodeRedirectTest extends TestCase
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

    public function test_codes_redirects_to_public_item_show_when_validation_true(): void
    {
        config(['app.locale' => 'en', 'app.fallback_locale' => 'en']);
        app()->setLocale('en');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Category']);

        $collaborator = Collaborator::factory()->create();
        $location = Location::query()->where('code', 'UTFPR')->first()
            ?? Location::query()->orderBy('id')->firstOrFail();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => '000',
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $location->id,
        ]);

        $enId = (int) Language::query()->where('code', 'en')->value('id');
        $item->translations()->delete();
        $item->translations()->create([
            'language_id' => $enId,
            'name' => 'Keyboard Satellite XP3',
            'description' => 'Description',
            'history' => null,
            'detail' => null,
        ]);
        $item->refresh();

        $code = ItemIdentificationCode::buildForItem(
            $item,
            'Keyboard Satellite XP3',
            (string) $location->code,
            $item->created_at
        );
        $item->update(['identification_code' => $code]);

        $response = $this->get('/codes/' . rawurlencode($code));

        $response->assertRedirect(route('catalog.items.show', ['id' => $item->id], false));
        $response->assertStatus(302);
    }

    public function test_codes_returns_404_when_item_not_validated(): void
    {
        config(['app.locale' => 'en']);
        app()->setLocale('en');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Category']);

        $collaborator = Collaborator::factory()->create();
        $location = Location::query()->orderBy('id')->firstOrFail();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => '000',
            'validation' => false,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $location->id,
        ]);

        $enId = (int) Language::query()->where('code', 'en')->value('id');
        $item->translations()->create([
            'language_id' => $enId,
            'name' => 'Draft museum item',
            'description' => 'Description',
            'history' => null,
            'detail' => null,
        ]);
        $item->refresh();

        $code = ItemIdentificationCode::buildForItem(
            $item,
            'Draft museum item',
            (string) $location->code,
            $item->created_at
        );
        $item->update(['identification_code' => $code]);

        $this->get('/codes/' . rawurlencode($code))->assertNotFound();
    }

    public function test_codes_returns_404_when_code_does_not_match_row(): void
    {
        config(['app.locale' => 'en']);
        app()->setLocale('en');

        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Category']);

        $collaborator = Collaborator::factory()->create();
        $location = Location::query()->orderBy('id')->firstOrFail();

        $item = Item::query()->create([
            'date' => null,
            'identification_code' => '1_UTFPR_REAL_26',
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $location->id,
        ]);

        $this->get('/codes/' . $item->id . '_UTFPR_FAKE_26')->assertNotFound();
    }
}
