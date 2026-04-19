<?php

namespace Tests\Feature\Catalog;

use App\Enums\Catalog\ItemImageType;
use App\Enums\Collaborator\CollaboratorRole;
use App\Mail\ItemContributionReceivedMail;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Mail, Storage};
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\MinimalContributionCoverJpeg;

#[Group('mysql')]
class ItemContributionStoreDateTest extends AbstractMysqlRefreshDatabaseTestCase
{
    /**
     * Avoid UploadedFile::fake()->image(): it requires GD (imagejpeg), which CI/host PHP may omit.
     */
    private function contributionCoverUploadedFile(string $filename = 'cover.jpg'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $filename,
            MinimalContributionCoverJpeg::binary()
        );
    }

    public function test_contribution_store_persists_release_date(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $cover = $this->contributionCoverUploadedFile();

        Mail::fake();
        Storage::fake('public');

        $email = 'emuseu.contrib.' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Test Contributor',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $response = $this->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->post(route('catalog.items.store'), [
            'full_name' => 'Test Contributor',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Item with date',
            'date' => '2015-06-20',
            'description' => 'Minimal description for validation.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'location_id' => $this->contributionLocationId(),
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertRedirect(route('catalog.items.create'));
        $response->assertSessionHas('success');

        $item = Item::query()->where('category_id', $category->id)->latest('id')->first();
        $this->assertNotNull($item);
        $this->assertSame('2015-06-20', $item->date?->format('Y-m-d'));

        $coverRow = $item->images()->where('type', ItemImageType::COVER)->first();
        $this->assertNotNull($coverRow);
        $storedPath = $coverRow->getRawOriginal('path');
        $this->assertStringStartsWith('items/' . $item->id . '/', (string) $storedPath);
        Storage::disk('public')->assertExists((string) $storedPath);

        Mail::assertSent(ItemContributionReceivedMail::class);
    }

    public function test_contribution_updates_collaborator_full_name_when_submitted_name_differs_from_record(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $cover = $this->contributionCoverUploadedFile();

        $email = 'emuseu.name-mismatch.' . uniqid('', false) . '@google.com';
        $collaborator = Collaborator::create([
            'full_name' => 'Registered Name',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        Mail::fake();

        $response = $this->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->from(route('catalog.items.create'))->post(route('catalog.items.store'), [
            'full_name' => 'Other Person',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Test item',
            'date' => '',
            'description' => 'Minimal description.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'location_id' => $this->contributionLocationId(),
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertRedirect(route('catalog.items.create'));
        $response->assertSessionHas('success');

        $collaborator->refresh();
        $this->assertSame('Other Person', $collaborator->full_name);

        $this->assertSame(1, Item::query()->where('category_id', $category->id)->count());
        Mail::assertSent(ItemContributionReceivedMail::class);
    }

    public function test_contribution_rejected_when_db_verified_but_no_session_auth(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $cover = $this->contributionCoverUploadedFile();

        $email = 'emuseu.no-session.' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Test Contributor',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $response = $this->post(route('catalog.items.store'), [
            'full_name' => 'Test Contributor',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'No session',
            'date' => '',
            'description' => 'Minimal description.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'location_id' => $this->contributionLocationId(),
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame(0, Item::query()->where('category_id', $category->id)->count());
    }

    public function test_contribution_without_verification_does_not_create_collaborator_for_new_email(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $cover = $this->contributionCoverUploadedFile();

        Mail::fake();

        $email = 'emuseu.no-orphan.' . uniqid('', false) . '@google.com';
        $this->assertNull(Collaborator::query()->where('email', $email)->first());

        $response = $this->post(route('catalog.items.store'), [
            'full_name' => 'New Contributor',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Item without verification',
            'date' => '',
            'description' => 'Minimal description.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'location_id' => $this->contributionLocationId(),
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertNull(Collaborator::query()->where('email', $email)->first());
        $this->assertSame(0, Item::query()->where('category_id', $category->id)->count());
    }

    private function contributionLocationId(): string
    {
        $id = CatalogLocationDefaultResolver::defaultLocationId();
        if ($id !== null) {
            return (string) $id;
        }

        $fallback = Location::query()->orderBy('id')->value('id');
        $this->assertNotNull($fallback, 'At least one location row is required for contribution tests.');

        return (string) $fallback;
    }
}
