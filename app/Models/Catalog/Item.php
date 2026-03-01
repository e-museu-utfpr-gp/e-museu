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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read string $image_url
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
        'category_id',
        'collaborator_id',
    ];

    protected $table = 'items';

    protected $casts = [
        'date' => 'date',
        'validation' => 'boolean',
    ];

    /**
     * Image path in storage. For legacy full URLs (http/https), getter returns '' (use image_url for display).
     * Nullable: null when item has no image yet.
     *
     * @return Attribute<string, ?string>
     */
    public function image(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->normalizeToPath($this->attributes['image'] ?? ''),
            set: function (?string $value): ?string {
                if ($value === null || $value === '') {
                    return null;
                }

                return $value;
            },
        );
    }

    /**
     * Builds storage path for item image: items/{id}/{uuid}_{id}.{ext}
     * Used by seeder and when creating/updating items with uploads.
     */
    public static function buildImagePath(self $item, string $extension = 'png'): string
    {
        $uuid = (string) Str::uuid7();
        $ext = preg_match('/^[a-z0-9]+$/i', $extension) ? strtolower($extension) : 'png';

        return sprintf('items/%s/%s_%s.%s', $item->id, $uuid, $item->id, $ext);
    }

    /**
     * Public URL for displaying the image (e.g. <img> src). Supports storage paths and legacy full URLs.
     *
     * @return Attribute<string, never>
     */
    public function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $raw = $this->attributes['image'] ?? null;
            if ($raw === null || $raw === '') {
                return '';
            }
            if (str_starts_with($raw, 'http')) {
                return $raw;
            }

            return Storage::disk('public')->url($raw);
        });
    }

    /** For legacy URLs we have no storage path; return '' so only image_url is used for display. */
    private function normalizeToPath(string $value): string
    {
        if ($value === '' || ! str_starts_with($value, 'http')) {
            return $value;
        }

        return '';
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
