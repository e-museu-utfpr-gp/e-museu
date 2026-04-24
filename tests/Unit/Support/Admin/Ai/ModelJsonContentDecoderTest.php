<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin\Ai;

use App\Support\Admin\Ai\ModelJsonContentDecoder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ModelJsonContentDecoderTest extends TestCase
{
    private ?ModelJsonContentDecoder $decoder = null;

    private function decoder(): ModelJsonContentDecoder
    {
        return $this->decoder ??= new ModelJsonContentDecoder();
    }

    public function test_empty_string_returns_empty_array(): void
    {
        $this->assertSame([], $this->decoder()->decodeAssoc(''));
        $this->assertSame([], $this->decoder()->decodeAssoc('   '));
    }

    public function test_plain_json_object(): void
    {
        $out = $this->decoder()->decodeAssoc('{"name":"X","slug":"y"}');

        $this->assertSame(['name' => 'X', 'slug' => 'y'], $out);
    }

    public function test_json_wrapped_in_markdown_fence(): void
    {
        $raw = "```json\n{\"title\":\"Hello\"}\n```";

        $this->assertSame(['title' => 'Hello'], $this->decoder()->decodeAssoc($raw));
    }

    public function test_json_with_leading_and_trailing_noise(): void
    {
        $raw = "Here is the result:\n{\"ok\":true}\nthanks.";

        $this->assertSame(['ok' => true], $this->decoder()->decodeAssoc($raw));
    }

    public function test_json_slice_ignores_braces_inside_string_values(): void
    {
        $raw = 'Preamble {"name": "Label with } char", "slug": "y"} trailing';

        $this->assertSame(['name' => 'Label with } char', 'slug' => 'y'], $this->decoder()->decodeAssoc($raw));
    }

    public function test_invalid_json_returns_empty_array(): void
    {
        $this->assertSame([], $this->decoder()->decodeAssoc('not json at all'));
    }

    /**
     * @param  array<string, mixed>  $expected
     */
    #[DataProvider('nestedValuesProvider')]
    public function test_preserves_nested_structure(string $json, array $expected): void
    {
        $this->assertSame($expected, $this->decoder()->decodeAssoc($json));
    }

    /**
     * @return iterable<string, array{0: string, 1: array<string, mixed>}>
     */
    public static function nestedValuesProvider(): iterable
    {
        yield 'nested object' => [
            '{"a":{"b":1}}',
            ['a' => ['b' => 1]],
        ];

        yield 'list value' => [
            '{"items":[1,2]}',
            ['items' => [1, 2]],
        ];
    }
}
