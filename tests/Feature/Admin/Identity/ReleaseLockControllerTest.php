<?php

namespace Tests\Feature\Admin\Identity;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Identity\Lock;
use App\Models\Location;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class ReleaseLockControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_cannot_release_lock(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.identity.release-lock'), [
            '_token' => session()->token(),
            'type' => 'items',
            'id' => 1,
        ])->assertRedirect(route('login'));
    }

    public function test_release_lock_returns_unprocessable_when_validation_fails(): void
    {
        $this->actingAs($this->createAdmin());

        $this->postJson(route('admin.identity.release-lock'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type', 'id']);
    }

    public function test_release_lock_returns_unprocessable_when_type_invalid(): void
    {
        $this->actingAs($this->createAdmin());

        $this->postJson(route('admin.identity.release-lock'), [
            'type' => 'not-a-lockable-type',
            'id' => 1,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    public function test_release_lock_returns_bad_request_when_lockable_route_missing_from_config(): void
    {
        $item = $this->createItemWithFixtures();

        $this->actingAs($this->createAdmin());

        $original = config('lockable_routes');
        config(['lockable_routes' => []]);

        try {
            $this->postJson(route('admin.identity.release-lock'), [
                'type' => 'items',
                'id' => $item->id,
            ])
                ->assertStatus(400);
        } finally {
            config(['lockable_routes' => $original]);
        }
    }

    public function test_release_lock_returns_not_found_when_subject_missing(): void
    {
        $item = $this->createItemWithFixtures();

        $this->actingAs($this->createAdmin());

        $this->postJson(route('admin.identity.release-lock'), [
            'type' => 'items',
            'id' => $item->id + 9_999_000,
        ])
            ->assertStatus(404);
    }

    public function test_release_lock_deletes_own_lock_and_returns_no_content(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        Lock::query()->create([
            'lockable_id' => $item->id,
            'lockable_type' => Item::class,
            'admin_id' => $admin->id,
            'expiry_date' => now()->addHour(),
        ]);

        $this->actingAs($admin);

        $this->postJson(route('admin.identity.release-lock'), [
            'type' => 'items',
            'id' => $item->id,
        ])
            ->assertNoContent();

        $this->assertDatabaseMissing('locks', [
            'lockable_id' => $item->id,
            'lockable_type' => Item::class,
            'admin_id' => $admin->id,
        ]);
    }

    public function test_release_lock_returns_no_content_without_deleting_other_admins_lock(): void
    {
        $holder = $this->createAdmin();
        $actor = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        Lock::query()->create([
            'lockable_id' => $item->id,
            'lockable_type' => Item::class,
            'admin_id' => $holder->id,
            'expiry_date' => now()->addHour(),
        ]);

        $this->actingAs($actor);

        $this->postJson(route('admin.identity.release-lock'), [
            'type' => 'items',
            'id' => $item->id,
        ])
            ->assertNoContent();

        $this->assertDatabaseHas('locks', [
            'lockable_id' => $item->id,
            'lockable_type' => Item::class,
            'admin_id' => $holder->id,
        ]);
    }

    public function test_release_lock_returns_no_content_when_subject_has_no_lock(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);

        $this->postJson(route('admin.identity.release-lock'), [
            'type' => 'items',
            'id' => $item->id,
        ])
            ->assertNoContent();
    }

    public function test_release_lock_accepts_collaborators_type(): void
    {
        $admin = $this->createAdmin();
        $collaborator = Collaborator::factory()->create();

        Lock::query()->create([
            'lockable_id' => $collaborator->id,
            'lockable_type' => Collaborator::class,
            'admin_id' => $admin->id,
            'expiry_date' => now()->addHour(),
        ]);

        $this->actingAs($admin);

        $this->postJson(route('admin.identity.release-lock'), [
            'type' => 'collaborators',
            'id' => $collaborator->id,
        ])
            ->assertNoContent();

        $this->assertDatabaseMissing('locks', [
            'lockable_id' => $collaborator->id,
            'lockable_type' => Collaborator::class,
            'admin_id' => $admin->id,
        ]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'release_lock_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createItemWithFixtures(array $overrides = []): Item
    {
        $categoryId = $overrides['category_id'] ?? ItemCategoryFactory::new()->create()->id;
        $locationId = $overrides['location_id'] ?? Location::factory()->create()->id;
        $collaboratorId = $overrides['collaborator_id'] ?? Collaborator::factory()->create()->id;

        return Item::factory()->create(array_merge([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ], $overrides));
    }
}
