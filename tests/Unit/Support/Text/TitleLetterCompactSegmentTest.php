<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Text;

use App\Support\Text\TitleLetterCompactSegment;
use PHPUnit\Framework\TestCase;

final class TitleLetterCompactSegmentTest extends TestCase
{
    public function test_from_raw_title_matches_identification_segment_rules(): void
    {
        $this->assertSame('KEP3', TitleLetterCompactSegment::fromRawTitle('Keyboard Satellite XP3'));
        $this->assertSame('TASF', TitleLetterCompactSegment::fromRawTitle('Tape K7 for BASF'));
    }

    public function test_from_raw_title_keeps_numeric_suffix_when_present(): void
    {
        $this->assertSame('PL25', TitleLetterCompactSegment::fromRawTitle('Placa de Rede 3DBI D-link DWA525'));
    }

    public function test_edge_letters_parameter(): void
    {
        $this->assertSame('KEP3', TitleLetterCompactSegment::fromRawTitle('Keyboard Satellite XP3', 2));
        $this->assertSame('K3', TitleLetterCompactSegment::fromRawTitle('Keyboard Satellite XP3', 1));
    }
}
