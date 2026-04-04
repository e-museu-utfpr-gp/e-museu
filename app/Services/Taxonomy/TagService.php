<?php

namespace App\Services\Taxonomy;

use App\Models\Language;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Builder;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Content\TranslatablePayload;
use App\Support\Content\TranslationDisplaySql;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
        $query->with('locks');

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

        $translations = $data['translations'] ?? [];
        $tag = Tag::create(Arr::except($data, ['translations']));
        $tag->syncTranslationsFromAdminForm($translations);

        return $tag;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateFromAdminRequestData(Tag $tag, array $data): void
    {
        $data['tag_category_id'] = $data['category_id'];
        unset($data['category_id']);

        $translations = $data['translations'] ?? [];
        $persist = Arr::except($data, ['translations']);
        if ($persist !== []) {
            $tag->update($persist);
        }
        if ($translations !== []) {
            $tag->syncTranslationsFromAdminForm($translations);
        }
    }

    public function deleteTag(Tag $tag): void
    {
        $tag->delete();
    }

    /**
     * Tags in a tag category for dependent selects (public catalog JSON): validated only.
     *
     * @return array{tags: Collection<int, Tag>, total: int, returned: int, truncated: bool}
     */
    public function getByCategoryValidatedOnly(string $categoryId): array
    {
        return $this->tagsByCategoryForSelect(
            $categoryId,
            static function (Builder $q): void {
                $q->where('validation', true);
            }
        );
    }

    /**
     * Tags in a category for admin selects (includes rows not yet validated).
     *
     * @return array{tags: Collection<int, Tag>, total: int, returned: int, truncated: bool}
     */
    public function getByCategoryIncludingUnvalidated(string $categoryId): array
    {
        return $this->tagsByCategoryForSelect($categoryId, null);
    }

    /**
     * @param  (callable(Builder<Tag>): void)|null  $restrictQuery  Optional scope (e.g. validated-only).
     * @return array{tags: Collection<int, Tag>, total: int, returned: int, truncated: bool}
     */
    private function tagsByCategoryForSelect(string $categoryId, ?callable $restrictQuery): array
    {
        if ($categoryId === '') {
            return [
                'tags' => new Collection(),
                'total' => 0,
                'returned' => 0,
                'truncated' => false,
            ];
        }

        $languageId = Language::idForPreferredFormLocale();
        $fallbackNameSql = TranslationDisplaySql::tagNameSubquerySql('tags');
        $resolvedNameSql = 'COALESCE(tt_pref.name, (' . $fallbackNameSql . '))';

        $totalQuery = Tag::query()->where('tag_category_id', $categoryId);
        if ($restrictQuery !== null) {
            $restrictQuery($totalQuery);
        }
        $total = $totalQuery->count();

        $tagsQuery = Tag::query()->where('tag_category_id', $categoryId);
        if ($restrictQuery !== null) {
            $restrictQuery($tagsQuery);
        }
        $tags = $tagsQuery
            ->leftJoin('tag_translations as tt_pref', function ($join) use ($languageId): void {
                $join->on('tags.id', '=', 'tt_pref.tag_id')
                    ->where('tt_pref.language_id', '=', $languageId);
            })
            ->select('tags.id')
            ->selectRaw("{$resolvedNameSql} AS name")
            ->orderByRaw("{$resolvedNameSql} asc")
            ->limit(500)
            ->get();

        $returned = $tags->count();

        return [
            'tags' => $tags,
            'total' => $total,
            'returned' => $returned,
            'truncated' => $total > $returned,
        ];
    }

    /**
     * JSON for public category tag select (validated tags only).
     *
     * @return array{
     *     data: array<int, array{id: int, name: string}>,
     *     meta: array{total: int, returned: int, truncated: bool}
     * }
     */
    public function jsonPayloadForPublicCategorySelect(string $categoryId): array
    {
        return $this->mapTagsByCategoryResultToJsonPayload($this->getByCategoryValidatedOnly($categoryId));
    }

    /**
     * JSON for admin category tag select (all tags in category).
     *
     * @return array{
     *     data: array<int, array{id: int, name: string}>,
     *     meta: array{total: int, returned: int, truncated: bool}
     * }
     */
    public function jsonPayloadForAdminCategorySelect(string $categoryId): array
    {
        return $this->mapTagsByCategoryResultToJsonPayload($this->getByCategoryIncludingUnvalidated($categoryId));
    }

    /**
     * @param  array{tags: Collection<int, Tag>, total: int, returned: int, truncated: bool}  $result
     * @return array{
     *     data: array<int, array{id: int, name: string}>,
     *     meta: array{total: int, returned: int, truncated: bool}
     * }
     */
    private function mapTagsByCategoryResultToJsonPayload(array $result): array
    {
        return [
            'data' => $result['tags']
                ->map(static fn (Tag $tag): array => [
                    'id' => (int) $tag->id,
                    'name' => (string) $tag->name,
                ])
                ->values()
                ->all(),
            'meta' => [
                'total' => $result['total'],
                'returned' => $result['returned'],
                'truncated' => $result['truncated'],
            ],
        ];
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

    /**
     * @return Collection<int, Tag>
     */
    public function getValidatedNamesForAutocompleteForLanguage(
        string $query,
        string $categoryId,
        int $languageId
    ): Collection {
        if ($categoryId === '') {
            return new Collection();
        }

        $qb = Tag::query()
            ->where('tag_category_id', $categoryId)
            ->where('validation', true)
            ->join('tag_translations', function ($join) use ($languageId): void {
                $join->on('tags.id', '=', 'tag_translations.tag_id')
                    ->where('tag_translations.language_id', '=', $languageId);
            })
            ->select('tags.id')
            ->addSelect(['tag_translations.name as name']);

        if ($query !== '') {
            $qb->where('tag_translations.name', 'LIKE', '%' . $query . '%');
        }

        return $qb->orderBy('tag_translations.name')->limit(50)->get();
    }

    public function countValidatedByNameAndCategory(string $name, string $categoryId, ?int $languageId = null): int
    {
        $langId = $languageId ?? Language::idForPreferredFormLocale();

        return Tag::query()
            ->where('tag_category_id', $categoryId)
            ->where('validation', true)
            ->whereHas('translations', function ($q) use ($name, $langId): void {
                $q->where('language_id', $langId)
                    ->where('name', '=', $name);
            })
            ->count();
    }

    /**
     * @param  array<string, mixed>  $tagData  Must contain 'name'; use 'tag_category_id' or 'category_id'.
     */
    public function findOrCreate(array $tagData, ?int $contentLanguageId = null): Tag
    {
        $tagCategoryId = $tagData['tag_category_id'] ?? $tagData['category_id'] ?? null;
        $normalized = $tagData;
        $normalized['tag_category_id'] = $tagCategoryId;
        unset($normalized['category_id']);
        $split = TranslatablePayload::split($normalized, TranslatablePayload::TAG_KEYS);
        $tagName = trim((string) ($split['translation']['name'] ?? ''));
        $langId = $contentLanguageId ?? Language::idForPreferredFormLocale();

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
        $tag->syncTranslationForLanguage($langId, ['name' => $tagName]);

        return $tag;
    }
}
