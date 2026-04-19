<?php

namespace Tests\Unit\Actions\Catalog;

use App\Actions\Catalog\StoreItemContributionAction;
use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class StoreItemContributionActionTest extends ServiceMysqlTestCase
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

    public function test_handle_returns_email_unverified_when_collaborator_missing(): void
    {
        /** @var ItemCategory $category */
        $category = ItemCategory::factory()->create();
        $action = app(StoreItemContributionAction::class);

        $result = $action->handle(
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

    public function test_handle_returns_internal_blocked_for_internal_collaborator(): void
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
        $result = $action->handle(
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

    public function test_handle_returns_collaborator_blocked_when_blocked(): void
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
        $result = $action->handle(
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

    public function test_handle_returns_email_unverified_when_session_not_authenticated(): void
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
        $result = $action->handle(
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

    public function test_handle_ok_creates_item_with_translation(): void
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
        $result = $action->handle(
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
        $item = $result['item'] ?? null;
        $this->assertInstanceOf(Item::class, $item);
        $this->assertSame($category->id, (int) $item->category_id);
        $this->assertNotSame('000', (string) $item->identification_code);
        $item->load('translations');
        $this->assertGreaterThan(0, $item->translations->count());
    }
}
