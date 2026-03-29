<?php

namespace App\Models\Catalog;

use App\Models\Identity\Lock;
use App\Models\Language;
use App\Support\Content\ResolvedTranslation;
use App\Support\Content\TranslationResolution;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Item categories have no text columns on the parent table: titles and labels live only in
 * {@see ItemCategoryTranslation} via {@see translations()}.
 * {@see ItemCategory::$fillable} is empty so we do not expose columns that do not exist on the table;
 * typical creation: {@see save()} + {@see syncPrimaryLocaleTranslation()}.
 */
class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_categories';

    protected $fillable = [];

    public function translations(): HasMany
    {
        return $this->hasMany(ItemCategoryTranslation::class, 'item_category_id')
            ->orderBy('language_id')
            ->orderBy('id');
    }

    /**
     * Name in {@see Language::idForPreferredFormLocale()} (identification codes, selects).
     */
    public function defaultLocaleName(): string
    {
        $languageId = Language::idForPreferredFormLocale();
        $row = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('language_id', $languageId)
            : $this->translations()->where('language_id', $languageId)->first();

        return $row?->name ?? '';
    }

    public function resolveTranslation(): ResolvedTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return TranslationResolution::fromCollection($this->translations);
    }

    public function resolvedTranslation(): ?ItemCategoryTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof ItemCategoryTranslation ? $t : null;
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

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
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
