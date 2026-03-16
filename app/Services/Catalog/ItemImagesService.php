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
    public function storeImagesFromStoreRequest(Item $item, AdminStoreItemRequest $request): void
    {
        $coverFile = $request->file('cover_image');
        if ($coverFile instanceof UploadedFile && $coverFile->isValid()) {
            $this->storeCoverImage($item, $coverFile);
        }

        $galleryFiles = $request->file('gallery_images');
        if (is_array($galleryFiles)) {
            $validGalleryFiles = $this->filterValidGalleryFiles($galleryFiles);
            $this->storeGalleryImages($item, $validGalleryFiles);
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
        $coverFile = $request->file('image');
        if ($coverFile instanceof UploadedFile && $coverFile->isValid()) {
            /** @var \Illuminate\Support\Collection<int, ItemImage> $currentCovers */
            $currentCovers = $item->images()->where('type', 'cover')->get();
            $currentCovers->each(fn (ItemImage $img) => $this->deleteItemImageFromStorage($img));
            $this->storeCoverImage($item, $coverFile);
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
            $validGalleryFiles = $this->filterValidGalleryFiles($galleryFiles);
            $this->storeGalleryImages($item, $validGalleryFiles);
        }
    }

    /**
     * @param  array<int, UploadedFile>|null  $galleryFiles
     * @return array<int, UploadedFile>
     */
    public function filterValidGalleryFiles(?array $galleryFiles): array
    {
        if (! is_array($galleryFiles)) {
            return [];
        }

        return array_values(array_filter(
            $galleryFiles,
            static fn (mixed $file): bool => $file instanceof UploadedFile && $file->isValid()
        ));
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
            report(new RuntimeException(sprintf(
                'Failed to read uploaded cover image contents for item ID %s',
                (string) $item->id
            )));

            throw new RuntimeException(__('app.catalog.item.upload_read_failed'));
        }
        Storage::disk('public')->put($path, $contents);
        $item->images()->create(['path' => $path, 'type' => 'cover', 'sort_order' => 0]);
        $item->normalizeSingleCover();
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
        $item->normalizeSingleCover();
    }

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
     * @throws NotFoundHttpException when the image does not exist or does not belong to the item
     */
    public function deleteImageById(string $itemId, string $imageId): void
    {
        $item = Item::findOrFail($itemId);
        $image = ItemImage::findOrFail($imageId);
        $this->deleteImage($item, $image);
    }

    /**
     * @throws NotFoundHttpException when the image does not belong to the item
     */
    public function deleteImage(Item $item, ItemImage $image): void
    {
        if ((int) $image->item_id !== (int) $item->id) {
            report(new NotFoundHttpException('Image does not belong to the given item.'));

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
