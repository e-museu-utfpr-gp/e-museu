<?php

namespace App\Models\Taxonomy;

use App\Models\Identity\Lock;
use App\Models\Language;
use App\Support\Content\ResolvedTranslation;
use App\Support\Content\TranslationResolution;
use App\Support\Content\TranslationDisplaySql;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $name Resolved display label (subquery or translations).
 */
class TagCategory extends Model
{
    use HasFactory;

    protected $table = 'tag_categories';

    public function translations(): HasMany
    {
        return $this->hasMany(TagCategoryTranslation::class, 'tag_category_id')
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

    public function resolvedTranslation(): ?TagCategoryTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof TagCategoryTranslation ? $t : null;
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'tag_category_id')
            ->orderByRaw(TranslationDisplaySql::tagNameSubquerySql('tags') . ' asc');
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
