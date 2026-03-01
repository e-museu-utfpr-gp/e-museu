<?php

namespace App\Models\Catalog;

use App\Enums\ItemImageType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $item_id
 * @property string $path
 * @property ItemImageType $type
 * @property int $sort_order
 * @property string $image_url
 */
class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'path',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'type' => ItemImageType::class,
        'sort_order' => 'integer',
    ];

    /**
     * Builds storage path for an item image: items/{id}/{uuid}_{id}.{ext}
     */
    public static function buildPath(Item $item, string $extension = 'png'): string
    {
        $uuid = (string) Str::uuid7();
        $ext = preg_match('/^[a-z0-9]+$/i', $extension) ? strtolower($extension) : 'png';

        return sprintf('items/%s/%s_%s.%s', $item->id, $uuid, $item->id, $ext);
    }

    /**
     * @return Attribute<string, never>
     */
    public function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $path = $this->attributes['path'] ?? '';
            if ($path === '' || str_starts_with($path, 'http')) {
                return $path;
            }

            return Storage::disk('public')->url($path);
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
