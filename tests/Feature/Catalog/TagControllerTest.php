<?php

namespace Tests\Feature\Catalog;

use App\Models\Language;
use App\Models\Taxonomy\TagCategory;
use Database\Factories\Taxonomy\TagFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;

#[Group('mysql')]
class TagControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    use RequiresMysqlDriverConnection;

    public function test_tags_index_returns_empty_payload_when_category_missing(): void
    {
        $this->getJson(route('catalog.tags.index', ['category' => '']))
            ->assertOk()
            ->assertExactJson([
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'returned' => 0,
                    'truncated' => false,
                ],
            ]);
    }

    public function test_tags_index_returns_validated_tags_in_data(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $tag = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $needle = 'TagCtrlIndex_' . uniqid('', false);
        $tag->syncPrimaryLocaleTranslation(['name' => $needle]);

        $response = $this->getJson(route('catalog.tags.index', ['category' => (string) $category->id]))
            ->assertOk();

        $response->assertJsonFragment(['id' => $tag->id, 'name' => $needle]);
        $ids = array_column($response->json('data'), 'id');
        $this->assertContains($tag->id, $ids);
    }

    public function test_tags_index_excludes_unvalidated_tags(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $validated = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $validated->syncPrimaryLocaleTranslation(['name' => 'TagCtrlOk']);
        $draft = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => false,
        ]);
        $draft->syncPrimaryLocaleTranslation(['name' => 'TagCtrlDraft']);

        $data = $this->getJson(route('catalog.tags.index', ['category' => (string) $category->id]))
            ->assertOk()
            ->json('data');

        $ids = array_column($data, 'id');
        $this->assertContains($validated->id, $ids);
        $this->assertNotContains($draft->id, $ids);
    }

    public function test_tags_autocomplete_returns_empty_for_empty_category(): void
    {
        $this->getJson(route('catalog.tags.autocomplete', [
            'query' => 'x',
            'category' => '',
        ]))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_tags_autocomplete_returns_matching_validated_tags(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $tag = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $tag->syncPrimaryLocaleTranslation(['name' => 'UniqueTagAutoMarkerXYZ']);

        $this->getJson(route('catalog.tags.autocomplete', [
            'query' => 'UniqueTagAutoMarker',
            'category' => (string) $category->id,
        ]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $tag->id,
                'name' => 'UniqueTagAutoMarkerXYZ',
            ]);
    }

    public function test_tags_autocomplete_with_empty_query_returns_tags_in_category(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $tag = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $tag->syncPrimaryLocaleTranslation(['name' => 'EmptyQueryTagName']);

        $this->getJson(route('catalog.tags.autocomplete', [
            'query' => '',
            'category' => (string) $category->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $tag->id]);
    }

    public function test_tags_autocomplete_excludes_unvalidated_tags(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $validated = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $validated->syncPrimaryLocaleTranslation(['name' => 'SharedTagMarkerOnlyVal']);
        $draft = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => false,
        ]);
        $draft->syncPrimaryLocaleTranslation(['name' => 'SharedTagMarkerOnlyVal']);

        $rows = $this->getJson(route('catalog.tags.autocomplete', [
            'query' => 'SharedTagMarker',
            'category' => (string) $category->id,
        ]))
            ->assertOk()
            ->json();

        $ids = array_column($rows, 'id');
        $this->assertContains($validated->id, $ids);
        $this->assertNotContains($draft->id, $ids);
    }

    public function test_tags_autocomplete_returns_422_for_unknown_content_locale(): void
    {
        $this->getJson(route('catalog.tags.autocomplete', [
            'query' => 'x',
            'category' => '1',
            'content_locale' => '__invalid_locale__',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_locale']);
    }

    public function test_tags_autocomplete_with_valid_content_locale_returns_ok(): void
    {
        $enCode = Language::query()->where('code', 'en')->value('code');
        $this->assertNotNull($enCode, 'Expected languages migration to seed the `en` language row.');

        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $tag = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $enId = (int) Language::query()->where('code', 'en')->value('id');
        $tag->translations()->updateOrCreate(
            ['language_id' => $enId],
            ['name' => 'TagAutoEnLocaleRow'],
        );

        $this->getJson(route('catalog.tags.autocomplete', [
            'query' => 'TagAutoEnLocale',
            'category' => (string) $category->id,
            'content_locale' => (string) $enCode,
        ]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $tag->id,
                'name' => 'TagAutoEnLocaleRow',
            ]);
    }

    public function test_tags_check_name_returns_json_count(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $tag = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $exactName = 'ExactTagNameCount_' . uniqid('', false);
        $langId = Language::idForPreferredFormLocale();
        $tag->translations()->updateOrCreate(
            ['language_id' => $langId],
            ['name' => $exactName],
        );

        $hit = $this->getJson(route('catalog.tags.check-name', [
            'category' => (string) $category->id,
            'name' => $exactName,
        ]));
        $hit->assertOk();
        $hitBody = $hit->getContent();
        $this->assertIsString($hitBody);
        $this->assertSame(1, json_decode($hitBody, true));

        $miss = $this->getJson(route('catalog.tags.check-name', [
            'category' => (string) $category->id,
            'name' => $exactName . '_missing',
        ]));
        $miss->assertOk();
        $missBody = $miss->getContent();
        $this->assertIsString($missBody);
        $this->assertSame(0, json_decode($missBody, true));
    }

    public function test_tags_check_name_returns_422_for_unknown_content_locale(): void
    {
        $this->getJson(route('catalog.tags.check-name', [
            'category' => '1',
            'name' => 'x',
            'content_locale' => '__bad__',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_locale']);
    }
}
