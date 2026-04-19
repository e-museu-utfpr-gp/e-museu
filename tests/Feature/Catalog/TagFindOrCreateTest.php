<?php

namespace Tests\Feature\Catalog;

use App\Models\Taxonomy\{Tag, TagCategory};
use App\Services\Taxonomy\TagService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;

#[Group('mysql')]
class TagFindOrCreateTest extends AbstractMysqlRefreshDatabaseTestCase
{
    use RequiresMysqlDriverConnection;

    public function test_find_or_create_reuses_same_tag_for_same_category_and_name(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $category = TagCategory::query()->orderBy('id')->firstOrFail();

        $service = app(TagService::class);

        $first = $service->findOrCreate([
            'category_id' => $category->id,
            'name' => 'Shared tag name',
        ]);
        $second = $service->findOrCreate([
            'tag_category_id' => $category->id,
            'name' => 'Shared tag name',
        ]);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Tag::query()->where('tag_category_id', $category->id)->count());
    }
}
