<?php

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use App\Services\Catalog\ItemQrCodeService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('services')]
class ItemQrCodeServiceTest extends TestCase
{
    public function test_destination_url_for_item_uses_app_url_and_encodes_identification_code(): void
    {
        config(['app.url' => 'https://museum.example']);

        $item = new Item([
            'identification_code' => 'CODE/1',
        ]);

        $url = app(ItemQrCodeService::class)->destinationUrlForItem($item);

        $this->assertSame('https://museum.example/codes/' . rawurlencode('CODE/1'), $url);
    }

    public function test_is_qr_domain_compatible_accepts_null_and_matching_origin(): void
    {
        config(['app.url' => 'https://www.example.com/app']);

        $svc = app(ItemQrCodeService::class);

        $this->assertTrue($svc->isQrDomainCompatible(null));
        $this->assertTrue($svc->isQrDomainCompatible(''));
        $this->assertTrue($svc->isQrDomainCompatible('https://example.com/path'));
        $this->assertFalse($svc->isQrDomainCompatible('https://evil.example/'));
    }

    public function test_target_url_from_qr_image_decodes_path_basename(): void
    {
        $item = new Item();
        $item->forceFill(['id' => 1])->syncOriginal();
        $path = ItemImage::buildQrCodePath($item, 'https://decoded.example/x', 'png');

        $image = new ItemImage();
        $image->forceFill(['path' => $path])->syncOriginal();

        $svc = app(ItemQrCodeService::class);
        $this->assertSame('https://decoded.example/x', $svc->targetUrlFromQrImage($image));
    }

    public function test_target_url_from_qr_image_returns_null_for_null_image(): void
    {
        $this->assertNull(app(ItemQrCodeService::class)->targetUrlFromQrImage(null));
    }
}
