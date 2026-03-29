<?php

namespace App\Models\Taxonomy;

use App\Models\Catalog\Item;
use App\Models\Identity\Lock;
use App\Models\Language;
use App\Support\Content\ResolvedTranslation;
use App\Support\Content\TranslationResolution;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'validation',
        'tag_category_id',
    ];

    protected $table = 'tags';

    public function translations(): HasMany
    {
        return $this->hasMany(TagTranslation::class, 'tag_id')
            ->orderBy('language_id')
            ->orderBy('id');
    }

    /**
     * @param  array{name: string}  $fields
     */
    public function syncPrimaryLocaleTranslation(array $fields): void
    {
        $languageId = Language::idForPreferredFormLocale();
        $this->translations()->updateOrCreate(
            ['language_id' => $languageId],
            $fields
        );
    }

    public function resolveTranslation(): ResolvedTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return TranslationResolution::fromCollection($this->translations);
    }

    public function resolvedTranslation(): ?TagTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof TagTranslation ? $t : null;
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_tag', 'tag_id', 'item_id');
    }

    public function tagCategory(): BelongsTo
    {
        return $this->belongsTo(TagCategory::class, 'tag_category_id');
    }

    public function category(): BelongsTo
    {
        return $this->tagCategory();
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
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
}
