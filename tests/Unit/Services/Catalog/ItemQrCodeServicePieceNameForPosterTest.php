<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\{Item, ItemCategory, ItemTranslation};
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use App\Services\Catalog\ItemQrCodeService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
#[Group('services')]
final class ItemQrCodeServicePieceNameForPosterTest extends ServiceMysqlTestCase
{
    use RequiresMysqlDriverConnection;

    private function anySeededLocationId(): int
    {
        $id = Location::query()->orderBy('id')->value('id');
        $this->assertNotNull($id, 'Migration seed must create at least one location row.');

        return (int) $id;
    }

    private function createItemWithoutTranslations(string $code): Item
    {
        $itemCategory = new ItemCategory();
        $itemCategory->save();
        $itemCategory->syncPrimaryLocaleTranslation(['name' => 'Cat']);

        return Item::query()->create([
            'date' => null,
            'identification_code' => $code,
            'validation' => true,
            'category_id' => $itemCategory->id,
            'collaborator_id' => Collaborator::factory()->create()->id,
            'location_id' => $this->anySeededLocationId(),
        ]);
    }

    public function test_piece_name_prefers_portuguese_over_universal_and_english(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_PT');
        $ptId = Language::idForCode('pt_BR');
        $uniId = Language::idForCode('universal');
        $enId = Language::idForCode('en');

        foreach (
            [
                [$ptId, 'Nome em PT'],
                [$uniId, 'Nome universal'],
                [$enId, 'English name'],
            ] as [$langId, $name]
        ) {
            ItemTranslation::query()->create([
                'item_id' => $item->id,
                'language_id' => $langId,
                'name' => $name,
                'description' => '',
                'history' => null,
                'detail' => null,
            ]);
        }

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            'Nome em PT',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }

    public function test_piece_name_falls_back_to_universal_when_portuguese_empty(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_UNI');
        $ptId = Language::idForCode('pt_BR');
        $uniId = Language::idForCode('universal');
        $enId = Language::idForCode('en');

        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $ptId,
            'name' => '   ',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);
        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $uniId,
            'name' => 'Título universal',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);
        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $enId,
            'name' => 'Should not pick English first',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            'Título universal',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }

    public function test_piece_name_falls_back_to_lowest_language_id_when_pt_and_universal_missing_or_blank(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_EN_ONLY');
        $enId = Language::idForCode('en');

        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $enId,
            'name' => 'Only English',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            'Only English',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }

    public function test_piece_name_among_non_priority_locales_uses_lowest_language_id(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_ORDER');
        $suffix = bin2hex(random_bytes(4));
        $langLow = Language::query()->create([
            'code' => 'zz_low_' . $suffix,
            'name' => 'low',
        ]);
        $langHigh = Language::query()->create([
            'code' => 'zz_high_' . $suffix,
            'name' => 'high',
        ]);
        $lowId = min($langLow->id, $langHigh->id);
        $highId = max($langLow->id, $langHigh->id);
        $this->assertLessThan($highId, $lowId);

        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $lowId,
            'name' => 'First by id',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);
        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => $highId,
            'name' => 'Second by id',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            'First by id',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }

    public function test_piece_name_returns_empty_when_no_translation_rows(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_NONE');

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            '',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }

    public function test_piece_name_returns_empty_when_all_names_are_blank(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_BLANK');
        foreach (['pt_BR', 'universal', 'en'] as $code) {
            ItemTranslation::query()->create([
                'item_id' => $item->id,
                'language_id' => Language::idForCode($code),
                'name' => "\t  \n",
                'description' => '',
                'history' => null,
                'detail' => null,
            ]);
        }

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            '',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }

    public function test_piece_name_trims_whitespace_for_chosen_locale(): void
    {
        $item = $this->createItemWithoutTranslations('QR_NAME_TRIM');
        ItemTranslation::query()->create([
            'item_id' => $item->id,
            'language_id' => Language::idForCode('pt_BR'),
            'name' => '  Peça  ',
            'description' => '',
            'history' => null,
            'detail' => null,
        ]);

        $fresh = Item::query()->findOrFail($item->id);
        $this->assertSame(
            'Peça',
            app(ItemQrCodeService::class)->pieceNameForQrPoster($fresh),
        );
    }
}
