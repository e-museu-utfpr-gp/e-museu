<?php

declare(strict_types=1);

namespace Tests\Unit\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use InvalidArgumentException;
use Tests\TestCase;

class ItemImageBuildPathTest extends TestCase
{
    public function test_build_path_throws_when_item_has_no_primary_key(): void
    {
        $item = new Item();

        $this->expectException(InvalidArgumentException::class);
        ItemImage::buildPath($item, 'jpg');
    }

    public function test_build_path_uses_numeric_item_id_segments(): void
    {
        $item = new Item();
        $item->forceFill(['id' => 42]);

        $path = ItemImage::buildPath($item, 'png');

        $this->assertStringStartsWith('items/42/', $path);
        $this->assertStringEndsWith('_42.png', $path);
    }
}
