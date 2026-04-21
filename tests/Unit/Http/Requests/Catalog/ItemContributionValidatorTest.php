<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Catalog;

use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\CatalogLocationDefaultResolver;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\MinimalContributionCoverJpeg;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class ItemContributionValidatorTest extends ServiceMysqlTestCase
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
        $this->assertNotNull($fallback);

        return (int) $fallback;
    }

    private function coverFile(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'cover.jpg',
            MinimalContributionCoverJpeg::binary()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validItemFields(ItemCategory $category): array
    {
        return [
            'content_locale' => 'pt_BR',
            'name' => 'Item name',
            'description' => 'Required description with content.',
            'detail' => '',
            'history' => '',
            'date' => '2011-11-11',
            'category_id' => (string) $category->id,
            'location_id' => (string) $this->contributionLocationId(),
            'cover_image' => $this->coverFile(),
        ];
    }

    /**
     * @param  array<string, mixed>  $itemOverrides
     * @param  array<int, array<string, mixed>>  $tags
     * @param  array<int, array<string, mixed>>  $extras
     * @param  array<int, array<string, mixed>>  $components
     */
    private function requestForContribution(
        string $email,
        array $itemOverrides = [],
        array $tags = [],
        array $extras = [],
        array $components = [],
    ): Request {
        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();
        $item = array_replace($this->validItemFields($category), $itemOverrides);
        $cover = $item['cover_image'] ?? $this->coverFile();
        unset($item['cover_image']);

        return Request::create('/catalog/items', 'POST', array_merge([
            'full_name' => 'Contributor',
            'email' => $email,
        ], $item, [
            'tags' => $tags,
            'extras' => $extras,
            'components' => $components,
        ]), [], [
            'cover_image' => $cover instanceof UploadedFile ? $cover : $this->coverFile(),
        ]);
    }

    public function test_validate_store_rejects_empty_description(): void
    {
        $email = 'contrib.val.' . uniqid('', true) . '@example.com';
        Collaborator::factory()->create([
            'email' => $email,
            'blocked' => false,
        ]);
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);

        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();
        $request = Request::create('/catalog/items', 'POST', [
            'full_name' => 'Contributor',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'X',
            'description' => '   ',
            'category_id' => (string) $category->id,
            'location_id' => (string) $this->contributionLocationId(),
        ], [], ['cover_image' => $this->coverFile()]);

        $this->expectException(ValidationException::class);
        app(ItemContributionValidator::class)->validateStore($request);
    }

    public function test_validate_store_rejects_name_longer_than_200(): void
    {
        $email = 'contrib.len.' . uniqid('', true) . '@example.com';
        Collaborator::factory()->create(['email' => $email, 'blocked' => false]);
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);

        $this->expectException(ValidationException::class);
        app(ItemContributionValidator::class)->validateStore($this->requestForContribution($email, [
            'name' => str_repeat('n', 201),
        ]));
    }

    public function test_validate_store_rejects_invalid_content_locale(): void
    {
        $email = 'contrib.loc.' . uniqid('', true) . '@example.com';
        Collaborator::factory()->create(['email' => $email, 'blocked' => false]);
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);

        $this->expectException(ValidationException::class);
        app(ItemContributionValidator::class)->validateStore($this->requestForContribution($email, [
            'content_locale' => 'xx_YY',
        ]));
    }

    public function test_validate_store_rejects_unknown_category_id(): void
    {
        $email = 'contrib.cat.' . uniqid('', true) . '@example.com';
        Collaborator::factory()->create(['email' => $email, 'blocked' => false]);
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);

        $this->expectException(ValidationException::class);
        app(ItemContributionValidator::class)->validateStore($this->requestForContribution($email, [
            'category_id' => '999999999',
        ]));
    }

    public function test_validate_store_rejects_duplicate_tag_in_request(): void
    {
        $email = 'contrib.tag.' . uniqid('', true) . '@example.com';
        Collaborator::factory()->create(['email' => $email, 'blocked' => false]);
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);

        $tags = [
            ['category_id' => '1', 'name' => 'DupTag'],
            ['category_id' => '1', 'name' => '  duptag  '],
        ];

        $this->expectException(ValidationException::class);
        app(ItemContributionValidator::class)->validateStore($this->requestForContribution($email, [], $tags));
    }

    public function test_validate_store_whitelists_nested_tag_and_extra_keys(): void
    {
        $email = 'contrib.ok.' . uniqid('', true) . '@example.com';
        Collaborator::factory()->create(['email' => $email, 'blocked' => false]);
        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(20)->getTimestamp(),
        ]);

        $tags = [
            ['category_id' => '1', 'name' => 'Tag label', 'hacker' => 'x'],
        ];
        $extras = [
            ['info' => 'Extra info line.', 'item_id' => 99, 'ignored' => 'y'],
        ];

        $out = app(ItemContributionValidator::class)->validateStore(
            $this->requestForContribution($email, [], $tags, $extras)
        );

        $this->assertSame(['category_id' => '1', 'name' => 'Tag label'], $out['tags'][0]);
        $this->assertSame(['info' => 'Extra info line.', 'item_id' => 99], $out['extras'][0]);
    }
}
