<?php

declare(strict_types=1);

namespace App\Services\Taxonomy;

use App\Support\Content\TranslationDisplaySql;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Taxonomy\{Tag, TagCategory};
use App\Support\Admin\{AdminIndexConfig, AdminIndexQueryBuilder};
use App\Support\Database\SqlExpr;
use Illuminate\Database\Eloquent\{Collection, Model};

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
            ->select('tag_categories.*');
        SqlExpr::selectRaw($query, "({$nameSql}) AS name");

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

        $categoriesQuery = TagCategory::query()
            ->select(['tag_categories.id'])
            ->orderBy('name')
            ->with([
                'tags.translations.language',
            ]);
        SqlExpr::selectRaw($categoriesQuery, "({$nameSql}) AS name");
        $categories = $categoriesQuery->get();

        $categories->each(function (TagCategory $category): void {
            $category->setRelation(
                'tags',
                $category->tags->sortBy(
                    function (Model $tag): string {
                        return mb_strtolower($tag instanceof Tag ? (string) $tag->name : '');
                    }
                )->values()
            );
        });

        return $categories;
    }

    /**
     * @return Collection<int, TagCategory>
     */
    public function getForForm(): Collection
    {
        $nameSql = TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories');

        $query = TagCategory::query()
            ->select('tag_categories.*')
            ->orderBy('name');
        SqlExpr::selectRaw($query, "({$nameSql}) AS name");

        return $query->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createTagCategory(array $data): TagCategory
    {
        $translations = $data['translations'] ?? [];
        $category = TagCategory::create(Arr::except($data, ['translations']));
        $category->syncTranslationsFromAdminForm($translations);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateTagCategory(TagCategory $tagCategory, array $data): void
    {
        $translations = $data['translations'] ?? [];
        $persist = Arr::except($data, ['translations']);
        if ($persist !== []) {
            $tagCategory->update($persist);
        }
        if ($translations !== []) {
            $tagCategory->syncTranslationsFromAdminForm($translations);
        }
    }

    public function deleteTagCategory(TagCategory $tagCategory): void
    {
        $tagCategory->delete();
    }
}
