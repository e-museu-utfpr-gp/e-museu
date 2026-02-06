<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 */
class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'history',
        'detail',
        'date',
        'identification_code',
        'validation',
        'image',
        'section_id',
        'proprietary_id',
    ];

    protected $table = 'items';

    public function proprietary(): BelongsTo
    {
        return $this->belongsTo(Proprietary::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_item', 'item_id', 'tag_id');
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function itemComponents(): HasMany
    {
        return $this->hasMany(ItemComponent::class);
    }

    public function tagItems(): HasMany
    {
        return $this->hasMany(TagItem::class);
    }

    public function locks(): MorphMany
    {
        return $this->morphMany(Lock::class, 'lockable');
    }
}
