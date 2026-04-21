<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\StringHelper;
use PHPUnit\Framework\TestCase;

final class StringHelperTest extends TestCase
{
    public function test_mb_take_prefix_and_suffix(): void
    {
        $this->assertSame('AB', StringHelper::mbTakePrefix('ABCDEF', 2));
        $this->assertSame('A', StringHelper::mbTakePrefix('A', 2));
        $this->assertSame('', StringHelper::mbTakePrefix('x', 0));
        $this->assertSame('EF', StringHelper::mbTakeSuffix('ABCDEF', 2));
        $this->assertSame('éal', StringHelper::mbTakeSuffix('Montréal', 3));
    }
}
