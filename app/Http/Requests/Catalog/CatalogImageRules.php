<?php

namespace App\Http\Requests\Catalog;

/**
 * Shared image validation for catalog items (public contribution vs admin).
 */
final class CatalogImageRules
{
    private const IMAGE_MIMES = 'jpeg,png,jpg,webp';

    private const MAX_KB = 10240;

    /**
     * @return array<string, string|array<int, string>>
     */
    public static function requiredCoverAndOptionalGallery(): array
    {
        return [
            'cover_image' => 'required|image|mimes:' . self::IMAGE_MIMES . '|max:' . self::MAX_KB,
            'gallery_images' => 'sometimes|array',
            'gallery_images.*' => 'image|mimes:' . self::IMAGE_MIMES . '|max:' . self::MAX_KB,
        ];
    }

    /**
     * Admin update: optional single upload, gallery add/remove/cover selection.
     *
     * @return array<string, string|array<int, string>>
     */
    public static function adminItemUpdate(): array
    {
        return [
            'image' => 'sometimes|image|mimes:' . self::IMAGE_MIMES . '|max:' . self::MAX_KB,
            'gallery_images' => 'sometimes|array',
            'gallery_images.*' => 'image|mimes:' . self::IMAGE_MIMES . '|max:' . self::MAX_KB,
            'delete_image_ids' => 'sometimes|array',
            'delete_image_ids.*' => 'integer|exists:item_images,id',
            'set_cover_image_id' => 'sometimes|nullable|integer|exists:item_images,id',
        ];
    }
}
