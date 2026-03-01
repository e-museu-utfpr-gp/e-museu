<?php

namespace App\Models\Catalog;

use App\Models\Identity\Lock;
use App\Models\Collaborator\Collaborator;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemImage> $images
 * @property-read ItemImage|null $coverImage
 * @property-read string $image_url
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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
        'category_id',
        'collaborator_id',
    ];

    protected $table = 'items';

    protected $casts = [
        'date' => 'date',
        'validation' => 'boolean',
    ];

    /**
     * Public URL for the item's cover image (first image with type cover, or first image).
     *
     * @return Attribute<string, never>
     */
    public function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $cover = $this->coverImage;

            return $cover?->image_url ?? optional($this->images()->orderBy('sort_order')->first())?->image_url ?? '';
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(ItemImage::class)->where('type', 'cover')->orderBy('sort_order');
    }

    /**
     * Ensure only one image has type=cover (the first by sort_order). Fixes legacy duplicate covers.
     */
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

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'item_tag', 'item_id', 'tag_id');
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
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
}
