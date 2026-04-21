<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Content;

use App\Support\Content\TranslatablePayload;
use PHPUnit\Framework\TestCase;

final class TranslatablePayloadTest extends TestCase
{
    public function test_split_separates_translation_and_persist_keys(): void
    {
        $data = ['name' => 'N', 'description' => 'D', 'id' => 1, 'validation' => true];
        $keys = ['name', 'description'];

        $out = TranslatablePayload::split($data, $keys);

        $this->assertSame(['name' => 'N', 'description' => 'D'], $out['translation']);
        $this->assertSame(['id' => 1, 'validation' => true], $out['persist']);
    }

    public function test_constants_are_non_empty_lists(): void
    {
        $this->assertNotEmpty(TranslatablePayload::ITEM_KEYS);
        $this->assertNotEmpty(TranslatablePayload::ITEM_CATEGORY_KEYS);
        $this->assertNotEmpty(TranslatablePayload::EXTRA_KEYS);
        $this->assertNotEmpty(TranslatablePayload::TAG_KEYS);
        $this->assertNotEmpty(TranslatablePayload::TAG_CATEGORY_KEYS);
    }
}
