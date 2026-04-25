<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Mail\ItemContributionReceivedMail;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use App\Services\Catalog\CatalogContributionReceivedMailService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class CatalogContributionReceivedMailServiceTest extends ServiceMysqlTestCase
{
    public function test_send_for_item_dispatches_mail_when_mailer_configured(): void
    {
        Mail::fake();

        $collaborator = Collaborator::factory()->create();
        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;

        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaborator->id,
        ]);

        $langId = Language::idForPreferredFormLocale();
        $item->syncTranslationForLanguage($langId, ['name' => 'Test item']);

        $svc = app(CatalogContributionReceivedMailService::class);
        $svc->sendForItem($item, $langId);

        Mail::assertSent(ItemContributionReceivedMail::class);
    }
}
