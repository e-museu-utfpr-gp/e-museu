<?php

namespace App\Services\Catalog;

use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Http\Requests\Admin\Catalog\AdminUpdateItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemImagesService
{
    /**
     * Store cover and gallery images from the admin store item request (new item creation).
     */
    public function storeImagesFromStoreRequest(Item $item, AdminStoreItemRequest $request): void
    {
        $coverFile = $request->file('cover_image');
        if ($coverFile instanceof UploadedFile && $coverFile->isValid()) {
            $this->storeCoverImage($item, $coverFile);
        }

        $galleryFiles = $request->file('gallery_images');
        if (is_array($galleryFiles)) {
            $validFiles = array_values(array_filter(
                $galleryFiles,
                fn ($f) => $f instanceof UploadedFile && $f->isValid()
            ));
            $this->storeGalleryImages($item, $validFiles);
        }

        $item->normalizeSingleCover();
    }

    public function processDeleteImageIds(Item $item, AdminUpdateItemRequest $request): void
    {
        $deleteIds = array_filter(array_map('intval', (array) $request->input('delete_image_ids', [])));
        if ($deleteIds === []) {
            return;
        }
        /** @var \Illuminate\Support\Collection<int, ItemImage> $toDelete */
        $toDelete = $item->images()->whereIn('id', $deleteIds)->get();
        $toDelete->each(fn (ItemImage $img) => $this->deleteItemImageFromStorage($img));
    }

    public function processCoverImage(Item $item, AdminUpdateItemRequest $request): void
    {
        if ($request->image) {
            /** @var \Illuminate\Support\Collection<int, ItemImage> $currentCovers */
            $currentCovers = $item->images()->where('type', 'cover')->get();
            $currentCovers->each(fn (ItemImage $img) => $this->deleteItemImageFromStorage($img));
            $this->storeCoverImage($item, $request->image);
            return;
        }
        if ($request->filled('set_cover_image_id')) {
            $this->promoteImageToCover($item, (int) $request->input('set_cover_image_id'));
        }
    }

    public function processGalleryImages(Item $item, AdminUpdateItemRequest $request): void
    {
        $galleryFiles = $request->file('gallery_images');
        if (is_array($galleryFiles)) {
            $this->storeGalleryImages($item, $galleryFiles);
        }
    }

    public function storeCoverImage(Item $item, UploadedFile $file): void
    {
        if (! $file->isValid()) {
            return;
        }
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $path = ItemImage::buildPath($item, $ext);
        $contents = $file->get();
        if ($contents === false) {
            throw new RuntimeException(__('app.catalog.item.upload_read_failed'));
        }
        Storage::disk('public')->put($path, $contents);
        $item->images()->create(['path' => $path, 'type' => 'cover', 'sort_order' => 0]);
    }

    /**
     * @param  array<int, UploadedFile>  $galleryFiles
     */
    public function storeGalleryImages(Item $item, array $galleryFiles): void
    {
        if ($galleryFiles === []) {
            return;
        }
        $maxOrder = (int) $item->images()->max('sort_order');
        foreach ($galleryFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }
            $contents = $file->get();
            if ($contents === false) {
                continue;
            }
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $path = ItemImage::buildPath($item, $ext);
            Storage::disk('public')->put($path, $contents);
            $item->images()->create(['path' => $path, 'type' => 'gallery', 'sort_order' => ++$maxOrder]);
        }
    }

    /**
     * Delete all images of an item from storage and remove their records, then remove the item's folder if it exists.
     */
    public function deleteAllImagesForItem(Item $item): void
    {
        foreach ($item->images as $img) {
            $this->deleteItemImageFromStorage($img);
        }
        $itemFolder = 'items/' . $item->id;
        if (Storage::disk('public')->exists($itemFolder)) {
            Storage::disk('public')->deleteDirectory($itemFolder);
        }
    }

    /**
     * Delete an item image by item id and image id (from route params).
     * @throws NotFoundHttpException when the image does not exist or does not belong to the item
     */
    public function deleteImageById(string $itemId, string $imageId): void
    {
        $item = Item::findOrFail($itemId);
        $image = ItemImage::findOrFail($imageId);
        $this->deleteImage($item, $image);
    }

    /**
     * Delete an item image from storage and remove its record.
     * @throws NotFoundHttpException when the image does not belong to the item
     */
    public function deleteImage(Item $item, ItemImage $image): void
    {
        if ($image->item_id !== (int) $item->id) {
            throw new NotFoundHttpException();
        }
        $this->deleteItemImageFromStorage($image);
    }

    private function deleteItemImageFromStorage(ItemImage $img): void
    {
        $path = $img->getRawOriginal('path');
        if ($path !== null && $path !== '' && ! str_starts_with((string) $path, 'http')) {
            Storage::disk('public')->delete($path);
        }
        $img->delete();
    }

    private function promoteImageToCover(Item $item, int $newCoverId): void
    {
        $newCover = $item->images()->find($newCoverId);
        if ($newCover === null) {
            return;
        }
        $item->images()->where('type', 'cover')->update(['type' => 'gallery']);
        $newCover->update(['type' => 'cover', 'sort_order' => 0]);
    }
}
