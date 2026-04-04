<?php

namespace App\Models\Catalog;

use App\Models\Identity\Lock;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Enums\Catalog\ItemImageType;
use App\Models\Taxonomy\Tag;
use App\Models\Taxonomy\TagCategory;
use App\Support\Content\ContentLocaleFallback;
use App\Support\Content\ResolvedTranslation;
use App\Support\Content\TranslationDisplaySql;
use App\Support\Content\TranslationResolution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemImage> $images
 * @property-read ItemImage|null $coverImage
 * @property-read ItemCategory|null $itemCategory
 * @property-read string|null $item_category_name
 * @property-read string $image_url
 * @property-read \App\Models\Catalog\ItemTranslation|null $translation Resolved row matching
 *     {@see TranslationResolution} / SQL fallback order (eager-load with `translation.language`).
 *
 * **Displayed translations (`name`, `description`, …):** in listings that inject columns via
 * {@see TranslationDisplaySql}, values come from SQL; when the model is loaded without those columns,
 * accessors use {@see resolveTranslation()} / the same fallback order in PHP. Keep both paths aligned.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'identification_code',
        'validation',
        'category_id',
        'collaborator_id',
    ];

    protected $table = 'items';

    protected $casts = [
        'date' => 'date',
        'validation' => 'boolean',
    ];

    private ?ResolvedTranslation $resolvedTranslationMemo = null;

    /**
     * @return Attribute<string, never>
     */
    public function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->relationLoaded('images')) {
                $sorted = $this->images->sortBy('sort_order');
                $img = $sorted->first(fn (ItemImage $i) => $i->type === ItemImageType::COVER)
                    ?? $sorted->first();

                return (string) ($img?->image_url ?? '');
            }

            $cover = $this->coverImage;

            return $cover?->image_url ?? optional($this->images()->orderBy('sort_order')->first())?->image_url ?? '';
        });
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ItemTranslation::class)->orderBy('language_id')->orderBy('id');
    }

    /**
     * Single translation row chosen with the same locale order as {@see TranslationResolution} and
     * {@see TranslationDisplaySql} (MySQL FIELD on `languages.code`).
     *
     * Prefer {@see resolveTranslation()} or SQL-injected columns ({@see scopeForAdminList}, catalog index query)
     * for collections: eager-loading this relation can produce heavy or awkward SQL at scale.
     */
    public function translation(): HasOne
    {
        $fieldList = ContentLocaleFallback::fieldListSql();

        return $this->hasOne(ItemTranslation::class)
            ->whereRaw(
                "item_translations.id = (
                    SELECT it_pick.id FROM item_translations it_pick
                    INNER JOIN languages lang_pick ON lang_pick.id = it_pick.language_id
                    WHERE it_pick.item_id = item_translations.item_id
                    ORDER BY FIELD(lang_pick.code, {$fieldList})
                    LIMIT 1
                )"
            );
    }

    public function resolveTranslation(): ResolvedTranslation
    {
        if ($this->resolvedTranslationMemo !== null) {
            return $this->resolvedTranslationMemo;
        }

        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $this->resolvedTranslationMemo = TranslationResolution::fromCollection($this->translations);

        return $this->resolvedTranslationMemo;
    }

    public function resolvedTranslation(): ?ItemTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof ItemTranslation ? $t : null;
    }

    /**
     * @param  array{name?: string, description?: string, history?: string|null, detail?: string|null}  $fields
     *
     * @see \App\Support\Content\TranslatablePayload::ITEM_KEYS
     * @see \App\Support\Content\TranslatablePayload::split()
     */
    public function syncPrimaryLocaleTranslation(array $fields): void
    {
        $this->syncTranslationForLanguage(Language::idForPreferredFormLocale(), $fields);
    }

    /**
     * @param  array{name?: string, description?: string, history?: string|null, detail?: string|null}  $fields
     */
    public function syncTranslationForLanguage(int $languageId, array $fields): void
    {
        $this->translations()->updateOrCreate(
            ['language_id' => $languageId],
            $fields
        );
        $this->resolvedTranslationMemo = null;
        if ($this->relationLoaded('translations')) {
            $this->unsetRelation('translations');
        }
    }

    /**
     * Upsert or remove `item_translations` rows from admin payloads keyed by {@see Language::code}.
     * Empty blocks (all fields blank) remove the row for that language.
     *
     * @param  array<string, array<string, mixed>|null>  $translationsByCode
     */
    public function syncTranslationsFromAdminForm(array $translationsByCode): void
    {
        foreach (Language::forAdminContentForms() as $lang) {
            $this->syncAdminFormTranslationBlock($lang, $translationsByCode[$lang->code] ?? []);
        }

        $this->resolvedTranslationMemo = null;
        if ($this->relationLoaded('translations')) {
            $this->unsetRelation('translations');
        }
    }

    private function syncAdminFormTranslationBlock(Language $lang, mixed $block): void
    {
        if (! is_array($block)) {
            return;
        }

        $name = trim((string) ($block['name'] ?? ''));
        $description = trim((string) ($block['description'] ?? ''));
        $detailRaw = trim((string) ($block['detail'] ?? ''));
        $detail = $detailRaw === '' ? null : $detailRaw;
        $historyRaw = trim((string) ($block['history'] ?? ''));
        $history = $historyRaw === '' ? null : $historyRaw;

        if ($name === '' && $description === '' && $detail === null && $history === null) {
            $this->translations()->where('language_id', $lang->id)->delete();

            return;
        }

        $this->translations()->updateOrCreate(
            ['language_id' => $lang->id],
            [
                'name' => $name,
                'description' => $description,
                'detail' => $detail,
                'history' => $history,
            ]
        );
    }

    public function refresh()
    {
        $this->resolvedTranslationMemo = null;

        return parent::refresh();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(ItemImage::class)->where('type', 'cover')->orderBy('sort_order');
    }

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'item_tag', 'item_id', 'tag_id')
            ->using(ItemTag::class)
            ->withPivot('validation');
    }

    public function composedOf(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_component', 'item_id', 'component_id');
    }

    public function composes(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_component', 'component_id', 'item_id');
    }

    public function extras(): HasMany
    {
        return $this->hasMany(Extra::class);
    }

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function itemComponents(): HasMany
    {
        return $this->hasMany(ItemComponent::class);
    }

    public function itemTags(): HasMany
    {
        return $this->hasMany(ItemTag::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }

    /**
     * Eager-load `tag.items` (+ images and translation languages) only for tags in the series category,
     * to avoid loading every related item for every tag on the public show page.
     */
    public function loadSeriesTimelineForCatalog(?int $seriesTagCategoryId): void
    {
        if ($seriesTagCategoryId === null) {
            return;
        }

        foreach ($this->itemTags as $itemTag) {
            $tag = $itemTag->tag;
            if ($tag === null || (int) $tag->tag_category_id !== $seriesTagCategoryId) {
                continue;
            }

            $tag->loadMissing([
                'items.images',
                'items.translations.language',
            ]);
        }
    }

    /**
     * Catalog item show: resolve stable series tag category id and eager-load series timeline data.
     */
    public function loadCatalogSeriesTimelineForShow(): ?int
    {
        $seriesCategoryId = TagCategory::idForSeriesCategory();
        $this->loadSeriesTimelineForCatalog($seriesCategoryId);

        return $seriesCategoryId;
    }

    /**
     * @param  Builder<Item>  $query
     * @return Builder<Item>
     */
    public function scopeWithCatalogShowRelations(Builder $query): Builder
    {
        return $query->with([
            'translations.language',
            'images',
            'itemCategory.translations.language',
            'collaborator',
            'itemTags.tag.translations.language',
            'itemTags.tag.tagCategory.translations.language',
            'itemComponents.component.images',
            'itemComponents.component.itemCategory.translations.language',
            'itemComponents.component.translations.language',
            'extras.collaborator',
            'extras.translations.language',
        ]);
    }

    /**
     * Relations for the read-only admin item show page (avoids N+1 on nested rows).
     *
     * @return list<string|array<string, mixed>>
     */
    public static function eagerLoadRelationsForAdminShow(): array
    {
        return [
            'translations.language',
            'images',
            'itemCategory.translations.language',
            'collaborator',
            'itemTags.tag.translations.language',
            'itemComponents.component.translations.language',
            'extras.collaborator',
            'extras.translations.language',
        ];
    }

    /**
     * @param  Builder<Item>  $query
     * @return Builder<Item>
     */
    public function scopeForAdminList(Builder $query): Builder
    {
        $nameSql = TranslationDisplaySql::itemTranslationSubquerySql('name', 'items');
        $historySql = TranslationDisplaySql::itemTranslationSubquerySql('history', 'items');
        $descSql = TranslationDisplaySql::itemTranslationSubquerySql('description', 'items');
        $detailSql = TranslationDisplaySql::itemTranslationSubquerySql('detail', 'items');
        $catNameSql = TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories');

        $query->with(['coverImage', 'locks'])
            ->leftJoin('collaborators', 'items.collaborator_id', '=', 'collaborators.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->select([
                'items.*',
                DB::raw("({$nameSql}) AS name"),
                'items.created_at AS item_created',
                'items.updated_at AS item_updated',
                'items.validation AS item_validation',
                DB::raw("LEFT(({$historySql}), 300) as history"),
                DB::raw("LEFT(({$descSql}), 150) as description"),
                DB::raw("LEFT(({$detailSql}), 150) as detail"),
                DB::raw("({$catNameSql}) AS item_category_name"),
                'collaborators.contact AS collaborator_contact',
            ]);

        return $query;
    }

    public function normalizeSingleCover(): void
    {
        $covers = $this->images()->where('type', 'cover')->orderBy('sort_order')->get();
        if ($covers->count() <= 1) {
            return;
        }
        $first = $covers->first();
        if ($first === null) {
            return;
        }
        $this->images()->where('type', 'cover')->where('id', '!=', $first->id)->update(['type' => 'gallery']);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::get(function (): string {
            if (array_key_exists('name', $this->attributes) && $this->attributes['name'] !== null) {
                return (string) $this->attributes['name'];
            }

            return (string) ($this->resolvedTranslation()?->name ?? '');
        });
    }

    /**
     * @return Attribute<string, never>
     */
    protected function description(): Attribute
    {
        return Attribute::get(function (): string {
            if (array_key_exists('description', $this->attributes) && $this->attributes['description'] !== null) {
                return (string) $this->attributes['description'];
            }

            return (string) ($this->resolvedTranslation()?->description ?? '');
        });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function history(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (array_key_exists('history', $this->attributes)) {
                return $this->attributes['history'] !== null ? (string) $this->attributes['history'] : null;
            }

            return $this->resolvedTranslation()?->history;
        });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function detail(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (array_key_exists('detail', $this->attributes)) {
                return $this->attributes['detail'] !== null ? (string) $this->attributes['detail'] : null;
            }

            return $this->resolvedTranslation()?->detail;
        });
    }
}
