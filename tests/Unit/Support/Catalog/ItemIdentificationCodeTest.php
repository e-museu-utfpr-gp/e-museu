<?php

namespace Tests\Unit\Support\Catalog;

use App\Models\Catalog\Item;
use App\Support\Catalog\ItemIdentificationCode;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

final class ItemIdentificationCodeTest extends TestCase
{
    public function test_name_segment_joins_first_and_last_letter_words(): void
    {
        $this->assertSame('KEP3', ItemIdentificationCode::nameSegmentFromTitle('Keyboard Satellite XP3'));
    }

    public function test_name_segment_single_word_uses_prefix_and_suffix_without_fixed_length(): void
    {
        $this->assertSame('AB', ItemIdentificationCode::nameSegmentFromTitle('Ab'));
        $this->assertSame('A', ItemIdentificationCode::nameSegmentFromTitle('A'));
        $this->assertSame('ABBC', ItemIdentificationCode::nameSegmentFromTitle('Abc'));
    }

    public function test_name_segment_keeps_letters_and_numbers_in_tokens(): void
    {
        $this->assertSame('TASF', ItemIdentificationCode::nameSegmentFromTitle('Tape K7 for BASF'));
        $this->assertSame('PL25', ItemIdentificationCode::nameSegmentFromTitle('Placa de Rede 3DBI D-link DWA525'));
    }

    public function test_parse_leading_id(): void
    {
        $this->assertSame(12, ItemIdentificationCode::parseLeadingId('12_UTFPR_AB_24'));
        $this->assertNull(ItemIdentificationCode::parseLeadingId('NOPE_12_34'));
        $this->assertNull(ItemIdentificationCode::parseLeadingId(''));
    }

    public function test_build_for_item_includes_segments_and_uppercases_location(): void
    {
        $item = new Item();
        $item->forceFill(['id' => 1]);

        $code = ItemIdentificationCode::buildForItem(
            $item,
            'Notebook ACER Aspire',
            'uncen',
            Carbon::parse('2026-04-06 10:00:00')
        );

        $this->assertSame('1_UNCEN_NORE_26', $code);
    }
}
