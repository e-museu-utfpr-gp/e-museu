<?php

declare(strict_types=1);

namespace App\Actions\Catalog\RegenerateItemQrCode\Concerns;

use App\Models\Catalog\Item;
use App\Services\Catalog\ItemQrCodeService;

/**
 * QR regeneration helpers backed by {@see ItemQrCodeService}.
 *
 * Composing class must expose {@see ItemQrCodeService} as `$this->itemQrCodeService` (typically constructor-injected).
 *
 * @property-read ItemQrCodeService $itemQrCodeService
 */
trait UsesItemQrCodeService
{
    protected function deleteExistingQrForItem(Item $item): void
    {
        $this->itemQrCodeService->deleteForItem($item);
    }

    protected function qrDestinationUrlForItem(Item $item): string
    {
        return $this->itemQrCodeService->destinationUrlForItem($item);
    }

    protected function qrPieceNameForPoster(Item $item): string
    {
        return $this->itemQrCodeService->pieceNameForQrPoster($item);
    }
}
