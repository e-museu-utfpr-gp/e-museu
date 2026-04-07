<?php

namespace App\Models\Taxonomy;

use App\Models\Concerns\SyncsAdminFormNameTranslations;
use App\Models\Identity\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Catalog\{Item, ItemTag};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, MorphMany};

class Tag extends Model
{
    use HasFactory;
    use SyncsAdminFormNameTranslations;

    protected $fillable = [
        'validation',
        'tag_category_id',
    ];

    protected $table = 'tags';

    protected $casts = [
        'validation' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(TagTranslation::class, 'tag_id')
            ->orderBy('language_id')
            ->orderBy('id');
    }

    public function resolvedTranslation(): ?TagTranslation
    {
        $t = $this->resolveTranslation()->translation;

        return $t instanceof TagTranslation ? $t : null;
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_tag', 'tag_id', 'item_id')
            ->using(ItemTag::class)
            ->withPivot('validation');
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
}
