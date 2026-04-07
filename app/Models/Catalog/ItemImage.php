<?php

namespace App\Models\Catalog;

use App\Enums\Catalog\ItemImageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

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
     *
     * Requires a persisted item primary key. An empty id would produce `items//…`, which
     * normalizes to files directly under `items/` with no per-item directory.
     */
    public static function buildPath(Item $item, string $extension = 'png'): string
    {
        $itemId = (int) $item->getKey();
        if ($itemId < 1) {
            throw new InvalidArgumentException(
                'Item must be saved with a valid id before building an image storage path.',
            );
        }

        $uuid = (string) Str::uuid7();
        $ext = preg_match('/^[a-z0-9]+$/i', $extension) ? strtolower($extension) : 'png';

        return sprintf('items/%d/%s_%d.%s', $itemId, $uuid, $itemId, $ext);
    }

    public static function buildQrCodePath(
        Item $item,
        string $targetUrl,
        string $extension = 'png'
    ): string {
        $itemId = (int) $item->getKey();
        if ($itemId < 1) {
            throw new InvalidArgumentException(
                'Item must be saved with a valid id before building a QRCode storage path.',
            );
        }

        $ext = preg_match('/^[a-z0-9]+$/i', $extension) ? strtolower($extension) : 'png';
        $encoded = rtrim(strtr(base64_encode($targetUrl), '+/', '-_'), '=');
        if ($encoded === '') {
            $encoded = 'unknown-target';
        }

        return sprintf('items/%d/qrcode/%s.%s', $itemId, $encoded, $ext);
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
