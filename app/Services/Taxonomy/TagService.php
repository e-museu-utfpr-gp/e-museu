<?php

namespace App\Services\Taxonomy;

use App\Models\Language;
use App\Models\Taxonomy\Tag;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Content\TranslatablePayload;
use App\Support\Content\TranslationDisplaySql;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TagService
{
    /**
     * @return array{tags: LengthAwarePaginator<int, Tag>, count: int}
     */
    public function getPaginatedTagsForAdminIndex(Request $request): array
    {
        $count = Tag::count();
        $tagNameSql = TranslationDisplaySql::tagNameSubquerySql('tags');
        $catNameSql = TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories');
        $query = Tag::query();
        $query->leftJoin('tag_categories', 'tags.tag_category_id', '=', 'tag_categories.id');
        $query->select([
            'tags.*',
            'tags.created_at AS tag_created',
            'tags.updated_at AS tag_updated',
        ]);
        $query->selectRaw("({$tagNameSql}) AS tag_name");
        $query->selectRaw("({$catNameSql}) AS category_name");

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::tags());

        $tags = $query->paginate(30)->withQueryString();

        return ['tags' => $tags, 'count' => $count];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createFromAdminRequestData(array $data): Tag
    {
        $data['tag_category_id'] = $data['category_id'];
        unset($data['category_id']);

        $split = TranslatablePayload::split($data, TranslatablePayload::TAG_KEYS);
        $tag = Tag::create($split['persist']);
        $tag->syncPrimaryLocaleTranslation([
            'name' => (string) ($split['translation']['name'] ?? ''),
        ]);

        return $tag;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateFromAdminRequestData(Tag $tag, array $data): void
    {
        $data['tag_category_id'] = $data['category_id'];
        unset($data['category_id']);

        $split = TranslatablePayload::split($data, TranslatablePayload::TAG_KEYS);
        if ($split['persist'] !== []) {
            $tag->update($split['persist']);
        }

        if (array_key_exists('name', $split['translation'])) {
            $tag->syncPrimaryLocaleTranslation([
                'name' => (string) $split['translation']['name'],
            ]);
        }
    }

    public function deleteTag(Tag $tag): void
    {
        $tag->delete();
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getByCategory(string $categoryId): Collection
    {
        if ($categoryId === '') {
            return new Collection();
        }

        $nameSql = TranslationDisplaySql::tagNameSubquerySql('tags');

        return Tag::query()
            ->where('tag_category_id', $categoryId)
            ->select('tags.*')
            ->orderByRaw("({$nameSql}) asc")
            ->limit(500)
            ->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getValidatedByCategory(string $categoryId): Collection
    {
        $nameSql = TranslationDisplaySql::tagNameSubquerySql('tags');

        return Tag::query()
            ->where('validation', true)
            ->where('tag_category_id', $categoryId)
            ->select('tags.id')
            ->selectRaw("({$nameSql}) AS name")
            ->orderByRaw("({$nameSql}) asc")
            ->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getValidatedNamesForAutocomplete(string $query, string $categoryId): Collection
    {
        if ($categoryId === '') {
            return new Collection();
        }

        $nameSql = TranslationDisplaySql::tagNameSubquerySql('tags');

        $qb = Tag::query()
            ->where('tag_category_id', $categoryId)
            ->where('validation', true)
            ->selectRaw("({$nameSql}) AS name");

        if ($query !== '') {
            $qb->whereRaw("({$nameSql}) LIKE ?", ['%' . $query . '%']);
        }

        return $qb->limit(50)->get();
    }

    public function countValidatedByNameAndCategory(string $name, string $categoryId): int
    {
        $langId = Language::idForPreferredFormLocale();

        return Tag::query()
            ->where('tag_category_id', $categoryId)
            ->where('validation', true)
            ->whereHas('translations', function ($q) use ($name, $langId): void {
                $q->where('language_id', $langId)
                    ->where('name', 'LIKE', $name);
            })
            ->count();
    }

    /**
     * @param  array<string, mixed>  $tagData  Must contain 'name'; use 'tag_category_id' or 'category_id'.
     */
    public function findOrCreate(array $tagData): Tag
    {
        $tagCategoryId = $tagData['tag_category_id'] ?? $tagData['category_id'] ?? null;
        $normalized = $tagData;
        $normalized['tag_category_id'] = $tagCategoryId;
        unset($normalized['category_id']);
        $split = TranslatablePayload::split($normalized, TranslatablePayload::TAG_KEYS);
        $tagName = (string) ($split['translation']['name'] ?? '');
        $langId = Language::idForPreferredFormLocale();

        $tag = Tag::query()
            ->where('tag_category_id', '=', $tagCategoryId)
            ->whereHas('translations', function ($q) use ($tagName, $langId): void {
                $q->where('language_id', $langId)->where('name', $tagName);
            })
            ->first();

        if ($tag !== null) {
            return $tag;
        }

        $tag = Tag::create($split['persist']);
        $tag->syncPrimaryLocaleTranslation(['name' => $tagName]);

        return $tag;
    }
}
