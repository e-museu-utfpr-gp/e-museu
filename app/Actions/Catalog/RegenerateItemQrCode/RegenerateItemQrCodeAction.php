<?php

declare(strict_types=1);

namespace App\Actions\Catalog\RegenerateItemQrCode;

use App\Actions\Catalog\RegenerateItemQrCode\Concerns\{
    ComposesQrCodePoster,
    GeneratesQrCodePngLocally,
    StoresRegeneratedQrItemImage,
    UsesItemQrCodeService,
};
use App\Exceptions\ItemQrRegenerateException;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use App\Services\Catalog\ItemQrCodeService;
use RuntimeException;

/**
 * Orchestrates QR regeneration; behaviour is split across traits under
 * {@see \App\Actions\Catalog\RegenerateItemQrCode\Concerns} (same idea as
 * {@see \App\Actions\Catalog\StoreItemContribution\StoreItemContributionAction}).
 *
 * @throws ItemQrRegenerateException
 */
final class RegenerateItemQrCodeAction
{
    use ComposesQrCodePoster;
    use GeneratesQrCodePngLocally;
    use StoresRegeneratedQrItemImage;
    use UsesItemQrCodeService;

    public function __construct(
        private readonly ItemQrCodeService $itemQrCodeService,
    ) {
    }

    public function handle(Item $item): ItemImage
    {
        $this->deleteExistingQrForItem($item);

        $targetUrl = $this->qrDestinationUrlForItem($item);
        $pngBody = $this->generateQrCodePngBodyForTargetUrl($targetUrl);
        $pieceName = $this->qrPieceNameForPoster($item);

        try {
            $pngBody = $this->composeQrCodePoster(
                $pngBody,
                (string) $item->identification_code,
                $pieceName,
            );
        } catch (RuntimeException $e) {
            throw new ItemQrRegenerateException('app.catalog.item.qrcode_regenerate_failed_compose', $e);
        }

        return $this->storeRegeneratedQrItemImage($item, $targetUrl, $pngBody);
    }
}
