<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Catalog\Item;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\Catalog\Item;
use App\Services\Catalog\ItemQrCodeService;
use App\Services\Identity\LockService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AdminItemQrCodeController extends AdminBaseController
{
    /**
     * Requires an edit lock but does not release it: the user stays on the edit screen and the lock
     * keeps protecting the record until they leave the flow (`docs/internal/edit-locks.md`).
     */
    public function regenerate(
        Item $item,
        ItemQrCodeService $itemQrCodeService,
        LockService $lockService,
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        try {
            $itemQrCodeService->regenerateForItem($item);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.catalog.items.edit', $item)
                ->withErrors(['qrcode' => __('app.catalog.item.qrcode_regenerate_failed')]);
        }

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item.qrcode_regenerated'));
    }

    /**
     * Same lock semantics as `regenerate()` (see `docs/internal/edit-locks.md`).
     */
    public function deleteQrCode(
        Item $item,
        ItemQrCodeService $itemQrCodeService,
        LockService $lockService,
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $itemQrCodeService->deleteForItem($item);

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item.qrcode_deleted'));
    }
}
