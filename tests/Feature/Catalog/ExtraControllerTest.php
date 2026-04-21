<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Mail\ExtraContributionReceivedMail;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Collaborator\CollaboratorService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class ExtraControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_extra_store_redirects_with_errors_when_payload_invalid(): void
    {
        $item = $this->createCatalogItem();

        $this->from(route('catalog.items.show', $item->id));

        $this->post(route('catalog.extras.store'), [
            '_token' => session()->token(),
        ])
            ->assertRedirect(route('catalog.items.show', $item->id))
            ->assertSessionHasErrors();
    }

    public function test_extra_store_requires_verified_contribution_session(): void
    {
        $item = $this->createCatalogItem();
        $collaborator = Collaborator::factory()->create([
            'last_email_verification_at' => now(),
        ]);

        Mail::fake();

        $this->from(route('catalog.items.show', $item->id));

        $this->post(route('catalog.extras.store'), [
            '_token' => session()->token(),
            'full_name' => $collaborator->full_name,
            'email' => $collaborator->email,
            'content_locale' => 'pt_BR',
            'info' => 'Test extra information.',
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
        ])
            ->assertRedirect(route('catalog.items.show', $item->id))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, Extra::query()->where('item_id', $item->id)->count());
        Mail::assertNothingSent();
    }

    public function test_extra_store_creates_extra_when_session_verified(): void
    {
        $item = $this->createCatalogItem();
        $collaborator = Collaborator::factory()->create([
            'last_email_verification_at' => now(),
        ]);

        Mail::fake();

        $response = $this->from(route('catalog.items.show', $item->id))->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $collaborator->email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->post(route('catalog.extras.store'), [
            '_token' => session()->token(),
            'full_name' => $collaborator->full_name,
            'email' => $collaborator->email,
            'content_locale' => 'pt_BR',
            'info' => 'Test extra information.',
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
        ]);

        $response->assertRedirect(route('catalog.items.show', $item->id));
        $response->assertSessionHas('success');

        $extra = Extra::query()->where('item_id', $item->id)->latest('id')->first();
        $this->assertNotNull($extra);
        $this->assertSame($collaborator->id, $extra->collaborator_id);
        $this->assertFalse($extra->validation);

        Mail::assertSent(ExtraContributionReceivedMail::class);
    }

    public function test_extra_store_rejects_email_mismatch_for_collaborator(): void
    {
        $item = $this->createCatalogItem();
        $collaborator = Collaborator::factory()->create([
            'last_email_verification_at' => now(),
        ]);

        $this->from(route('catalog.items.show', $item->id))->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $collaborator->email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->post(route('catalog.extras.store'), [
            '_token' => session()->token(),
            'full_name' => $collaborator->full_name,
            'email' => 'other_' . $collaborator->email,
            'content_locale' => 'pt_BR',
            'info' => 'Test extra information.',
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
        ])
            ->assertRedirect(route('catalog.items.show', $item->id))
            ->assertSessionHasErrors('email');
    }

    private function createCatalogItem(): Item
    {
        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaboratorId = Collaborator::factory()->create()->id;

        return Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ]);
    }
}
