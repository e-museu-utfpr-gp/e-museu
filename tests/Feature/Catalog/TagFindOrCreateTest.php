<?php

namespace Tests\Feature\Catalog;

use App\Services\Taxonomy\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use App\Models\Taxonomy\{Tag, TagCategory};

#[Group('mysql')]
class TagFindOrCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped(
                'Catalog tests require pdo_mysql (install the extension or run tests in the app Docker container).'
            );
        }

        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped(
                'Set DB_CONNECTION=mysql in .env.testing (catalog seeds and SQL assume MySQL).'
            );
        }
    }

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
