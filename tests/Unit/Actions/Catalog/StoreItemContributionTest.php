<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Catalog;

use App\Actions\Catalog\StoreItemContribution\StoreItemContributionAction;
use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Catalog\ItemComponent;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use App\Models\Taxonomy\TagCategory;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use Database\Factories\Catalog\ItemFactory;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class StoreItemContributionTest extends ServiceMysqlTestCase
{
    protected function tearDown(): void
    {
        session()->forget(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY);

        parent::tearDown();
    }

    private function contributionLocationId(): int
    {
        $id = CatalogLocationDefaultResolver::defaultLocationId();
        if ($id !== null) {
            return $id;
        }

        $fallback = Location::query()->orderBy('id')->value('id');
        $this->assertNotNull($fallback, 'At least one location row is required for contribution tests.');

        return (int) $fallback;
    }

    /**
     * @return array<string, mixed>
     */
    private function collaboratorPayload(string $email, string $fullName = 'Contributor'): array
    {
        return [
            'email' => $email,
            'full_name' => $fullName,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function minimalItemPayload(ItemCategory $category): array
    {
        return [
            'name' => 'Action test item',
            'description' => 'Minimal description.',
            'history' => '',
            'detail' => '',
            'date' => '2020-05-01',
            'validation' => false,
            'category_id' => $category->id,
            'location_id' => $this->contributionLocationId(),
        ];
    }

    private function putPublicContributionSession(string $email): void
    {
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);
    }

    public function test_persist_contribution_returns_email_unverified_when_collaborator_missing(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $action = app(StoreItemContributionAction::class);

        $result = $action->persistContribution(
            $this->collaboratorPayload('missing.' . uniqid('', true) . '@example.com'),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [],
            null,
            null,
        );

        $this->assertSame('email_unverified', $result['status']);
        $this->assertArrayNotHasKey('item', $result);
    }

    public function test_persist_contribution_returns_internal_blocked_for_internal_collaborator(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'internal.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'Internal',
            'email' => $email,
            'role' => CollaboratorRole::INTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [],
            null,
            null,
        );

        $this->assertSame('internal_blocked', $result['status']);
        $this->assertArrayNotHasKey('item', $result);
    }

    public function test_persist_contribution_returns_collaborator_blocked_when_blocked(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'blocked.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'Blocked',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => true,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [],
            null,
            null,
        );

        $this->assertSame('collaborator_blocked', $result['status']);
        $this->assertArrayNotHasKey('item', $result);
    }

    public function test_persist_contribution_returns_email_unverified_when_session_not_authenticated(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'external.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'External',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        session()->forget(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY);

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [],
            null,
            null,
        );

        $this->assertSame('email_unverified', $result['status']);
        $this->assertArrayNotHasKey('item', $result);
    }

    public function test_persist_contribution_ok_creates_item_with_translation(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'ok.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'Contributor OK',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email, 'Contributor OK'),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [],
            null,
            null,
        );

        $this->assertSame('ok', $result['status']);
        $this->assertArrayHasKey('item', $result);
        $item = $result['item'];
        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame($category->id, (int) $item->category_id);
        $this->assertNotSame('000', (string) $item->identification_code);
        $item->load('translations');
        $this->assertGreaterThan(0, $item->translations->count());
    }

    public function test_persist_contribution_attaches_tag_to_item(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'tags.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'Tagger',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $tagCategory = TagCategory::query()->orderBy('id')->firstOrFail();
        $tagName = 'contrib-tag-' . uniqid('', true);
        $tags = [[
            'tag_category_id' => $tagCategory->id,
            'name' => $tagName,
            'validation' => false,
        ]];

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            $tags,
            [],
            [],
            null,
            null,
        );

        $this->assertSame('ok', $result['status']);
        /** @var Item $item */
        $item = $result['item'];
        $langId = Language::idForCode('pt_BR');
        $item->load('tags.translations');
        $this->assertGreaterThanOrEqual(1, $item->tags->count());
        $matched = false;
        foreach ($item->tags as $tag) {
            $tr = $tag->translations->firstWhere('language_id', $langId);
            if ($tr !== null && trim((string) $tr->name) === $tagName) {
                $matched = true;
                break;
            }
        }
        $this->assertTrue($matched, 'Expected tag name in pt_BR translation.');
    }

    public function test_persist_contribution_creates_extra_for_item(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'extras.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'Extra contributor',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $extraInfo = 'Extra body ' . uniqid('', true);
        $extras = [[
            'info' => $extraInfo,
            'validation' => false,
        ]];

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            $extras,
            [],
            null,
            null,
        );

        $this->assertSame('ok', $result['status']);
        /** @var Item $item */
        $item = $result['item'];
        $langId = Language::idForCode('pt_BR');
        $item->load('extras.translations');
        $this->assertCount(1, $item->extras);
        $tr = $item->extras->first()?->translations->firstWhere('language_id', $langId);
        $this->assertNotNull($tr);
        $this->assertSame($extraInfo, trim((string) ($tr->info ?? '')));
    }

    public function test_persist_contribution_links_component_catalog_item(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'comp.action.' . uniqid('', true) . '@example.com';
        Collaborator::create([
            'full_name' => 'Composer',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $componentItem = ItemFactory::new()->create([
            'category_id' => $category->id,
            'location_id' => $this->contributionLocationId(),
            'validation' => true,
        ]);

        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [['item_id' => $componentItem->id]],
            null,
            null,
        );

        $this->assertSame('ok', $result['status']);
        /** @var Item $parent */
        $parent = $result['item'];
        $this->assertTrue(
            ItemComponent::query()
                ->where('item_id', $parent->id)
                ->where('component_id', $componentItem->id)
                ->where('validation', false)
                ->exists()
        );
    }

    public function test_persist_contribution_updates_collaborator_full_name_when_submission_differs(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'rename.action.' . uniqid('', true) . '@example.com';
        $collaborator = Collaborator::create([
            'full_name' => 'Name In Database',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $submittedName = 'Name From Contribution Form';
        $action = app(StoreItemContributionAction::class);
        $result = $action->persistContribution(
            $this->collaboratorPayload($email, $submittedName),
            $this->minimalItemPayload($category),
            Language::idForCode('pt_BR'),
            [],
            [],
            [],
            null,
            null,
        );

        $this->assertSame('ok', $result['status']);
        $collaborator->refresh();
        $this->assertSame($submittedName, $collaborator->full_name);
    }

    public function test_persist_contribution_invalid_component_id_throws_and_does_not_persist_item(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $email = 'badcomp.action.' . uniqid('', true) . '@example.com';
        $collaborator = Collaborator::create([
            'full_name' => 'Bad components',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $before = Item::query()->where('collaborator_id', $collaborator->id)->count();
        $action = app(StoreItemContributionAction::class);

        try {
            $action->persistContribution(
                $this->collaboratorPayload($email),
                $this->minimalItemPayload($category),
                Language::idForCode('pt_BR'),
                [],
                [],
                [['item_id' => 9_999_999_999]],
                null,
                null,
            );
            $this->fail('Expected ValidationException for unknown component item_id.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('components', $e->errors());
        }

        $this->assertSame(
            $before,
            Item::query()->where('collaborator_id', $collaborator->id)->count(),
            'Rolled-back contribution must not leave an item row for this collaborator.'
        );
    }

    public function test_persist_contribution_rejects_component_item_not_validated(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $draft = ItemFactory::new()->create([
            'category_id' => $category->id,
            'location_id' => $this->contributionLocationId(),
            'collaborator_id' => Collaborator::factory()->create()->id,
            'validation' => false,
        ]);

        $email = 'draftcomp.action.' . uniqid('', true) . '@example.com';
        $collaborator = Collaborator::create([
            'full_name' => 'Draft component',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $this->putPublicContributionSession($email);

        $before = Item::query()->where('collaborator_id', $collaborator->id)->count();
        $action = app(StoreItemContributionAction::class);

        try {
            $action->persistContribution(
                $this->collaboratorPayload($email),
                $this->minimalItemPayload($category),
                Language::idForCode('pt_BR'),
                [],
                [],
                [['item_id' => $draft->id]],
                null,
                null,
            );
            $this->fail('Expected ValidationException for unvalidated component item_id.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('components', $e->errors());
        }

        $this->assertSame($before, Item::query()->where('collaborator_id', $collaborator->id)->count());
    }
}
