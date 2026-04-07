<?php

namespace App\Models\Catalog;

use App\Models\Language;
use App\Models\Concerns\SyncsAdminFormNameTranslations;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany};

/**
 * Item categories have no text columns on the parent table: titles and labels live only in
 * {@see ItemCategoryTranslation} via {@see translations()}.
 * {@see ItemCategory::$fillable} is empty so we do not expose columns that do not exist on the table;
 * typical creation: {@see save()} + {@see syncPrimaryLocaleTranslation()}.
 */
class ItemCategory extends Model
{
    use HasFactory;
    use SyncsAdminFormNameTranslations;

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

    public function resolvedTranslation(): ?ItemCategoryTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof ItemCategoryTranslation ? $t : null;
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
