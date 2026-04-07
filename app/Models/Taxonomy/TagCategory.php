<?php

namespace App\Models\Taxonomy;

use App\Models\Concerns\SyncsAdminFormNameTranslations;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany};

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $name Resolved display label (subquery or translations).
 *
 * Text lives only in {@see TagCategoryTranslation}; {@see $fillable} is empty on the parent row.
 *
 * Series timeline: migration {@see 2026_03_29_000005_create_tag_categories_table} fixes id **2** as the series
 * category (“Série” / “Series”). Use {@see idForSeriesCategory()} instead of matching display names.
 */
class TagCategory extends Model
{
    use HasFactory;
    use SyncsAdminFormNameTranslations;

    protected $table = 'tag_categories';

    protected $fillable = [];

    /** Stable id from tag_categories migration (series / timeline). */
    private const SERIES_TAG_CATEGORY_ID = 2;

    public static function idForSeriesCategory(): ?int
    {
        if (! static::query()->whereKey(self::SERIES_TAG_CATEGORY_ID)->exists()) {
            return null;
        }

        return self::SERIES_TAG_CATEGORY_ID;
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TagCategoryTranslation::class, 'tag_category_id')
            ->orderBy('language_id')
            ->orderBy('id');
    }

    public function resolvedTranslation(): ?TagCategoryTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof TagCategoryTranslation ? $t : null;
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'tag_category_id')->orderBy('tags.id');
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
