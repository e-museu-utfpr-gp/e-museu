<?php

declare(strict_types=1);

namespace App\Actions\Catalog\RegenerateItemQrCode\Concerns;

use App\Exceptions\ItemQrRegenerateException;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Throwable;

trait GeneratesQrCodePngLocally
{
    /** Raster edge length fed into {@see ComposesQrCodePoster} (512 × 512 px). */
    private const int QR_CODE_IMAGE_SIZE_PX = 512;

    /**
     * @throws ItemQrRegenerateException
     */
    protected function generateQrCodePngBodyForTargetUrl(string $targetUrl): string
    {
        try {
            $qrCode = new QrCode(
                data: $targetUrl,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: self::QR_CODE_IMAGE_SIZE_PX,
                margin: 0,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255),
            );

            return (new PngWriter())->write($qrCode)->getString();
        } catch (Throwable $e) {
            throw new ItemQrRegenerateException('app.catalog.item.qrcode_regenerate_failed_encode', $e);
        }
    }
}
