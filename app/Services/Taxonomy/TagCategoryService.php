<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy\TagCategory;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Content\TranslatablePayload;
use App\Support\Content\TranslationDisplaySql;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TagCategoryService
{
    /**
     * @return array{tagCategories: LengthAwarePaginator<int, TagCategory>, count: int}
     */
    public function getPaginatedTagCategoriesForAdminIndex(Request $request): array
    {
        $count = TagCategory::count();
        $nameSql = TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories');
        $query = TagCategory::query()
            ->select('tag_categories.*')
            ->selectRaw("({$nameSql}) AS name");

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::tagCategories());

        $tagCategories = $query->paginate(30)->withQueryString();

        return ['tagCategories' => $tagCategories, 'count' => $count];
    }

    /**
     * @return Collection<int, TagCategory>
     */
    public function getForIndex(): Collection
    {
        $nameSql = TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories');

        return TagCategory::query()
            ->select(['tag_categories.id'])
            ->selectRaw("({$nameSql}) AS name")
            ->orderBy('name')
            ->with([
                'tags.translations.language',
            ])
            ->get();
    }

    /**
     * @return Collection<int, TagCategory>
     */
    public function getForForm(): Collection
    {
        $nameSql = TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories');

        return TagCategory::query()
            ->select('tag_categories.*')
            ->selectRaw("({$nameSql}) AS name")
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createTagCategory(array $data): TagCategory
    {
        $split = TranslatablePayload::split($data, TranslatablePayload::TAG_CATEGORY_KEYS);
        $category = TagCategory::create($split['persist']);
        $category->syncPrimaryLocaleTranslation([
            'name' => (string) ($split['translation']['name'] ?? ''),
        ]);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateTagCategory(TagCategory $tagCategory, array $data): void
    {
        $split = TranslatablePayload::split($data, TranslatablePayload::TAG_CATEGORY_KEYS);
        if ($split['persist'] !== []) {
            $tagCategory->update($split['persist']);
        }
        if (array_key_exists('name', $split['translation'])) {
            $tagCategory->syncPrimaryLocaleTranslation([
                'name' => (string) $split['translation']['name'],
            ]);
        }
    }

    public function deleteTagCategory(TagCategory $tagCategory): void
    {
        $tagCategory->delete();
    }
}
