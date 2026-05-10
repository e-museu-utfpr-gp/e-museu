<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Catalog\RegenerateItemQrCode;

use App\Actions\Catalog\RegenerateItemQrCode\Concerns\ComposesQrCodePoster;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\MinimalQrPng;
use Tests\TestCase;

#[Group('actions')]
class ComposesQrCodePosterTest extends TestCase
{
    public function test_compose_outputs_png_larger_than_input_when_logos_exist(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension not loaded.');
        }

        $qrBytes = MinimalQrPng::binary();

        $subject = new class {
            use ComposesQrCodePoster;

            public function composeForTest(string $qr, string $code, string $name = ''): string
            {
                return $this->composeQrCodePoster($qr, $code, $name);
            }
        };

        $out = $subject->composeForTest($qrBytes, 'ITEM-CODE-42', 'Nome da peça em português');

        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $out);
        $this->assertGreaterThan(strlen($qrBytes), strlen($out));
    }
}
