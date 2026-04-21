<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Catalog\Item;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\Catalog\{Item, ItemImage};
use App\Services\Catalog\ItemImagesService;
use App\Services\Identity\LockService;
use Illuminate\Http\RedirectResponse;

class AdminItemImageController extends AdminBaseController
{
    /**
     * Requires an edit lock but does not release it: the user stays on the edit screen and the lock
     * keeps protecting the record until they leave the flow (`docs/internal/edit-locks.md`).
     */
    public function destroy(
        Item $item,
        ItemImage $image,
        ItemImagesService $itemImagesService,
        LockService $lockService,
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $itemImagesService->deleteImage($item, $image);

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item_image.deleted'));
    }
}
