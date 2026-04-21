<?php

declare(strict_types=1);

namespace App\Support\Catalog;

use App\Models\Catalog\Item;
use App\Support\Text\TitleLetterCompactSegment;
use DateTimeInterface;

/**
 * Builds and parses public catalog item {@see Item::$identification_code} values.
 *
 * Format: `{id}_{location_code}_{name_segment}_{yy}` with underscores only between segments.
 * The name segment comes from {@see TitleLetterCompactSegment::fromRawTitle()}.
 */
final class ItemIdentificationCode
{
    /**
     * Human-readable title at creation time (e.g. one locale row).
     * Not a stored translation of the code.
     */
    public static function buildForItem(
        Item $item,
        string $itemTitle,
        string $locationCode,
        ?DateTimeInterface $createdAt
    ): string {
        $created = $createdAt ?? $item->created_at;
        $yy = $created !== null ? $created->format('y') : date('y');

        $loc = strtoupper(trim($locationCode));
        if ($loc === '') {
            $loc = 'INDEF';
        }

        $nameSeg = TitleLetterCompactSegment::fromRawTitle($itemTitle);

        $parts = [(string) $item->getKey(), $loc];
        if ($nameSeg !== '') {
            $parts[] = $nameSeg;
        }
        $parts[] = $yy;

        return implode('_', $parts);
    }

    /**
     * Leading digits before the first underscore; used for optimized resolution of `/codes/{code}`.
     *
     * @return positive-int|null
     */
    public static function parseLeadingId(string $code): ?int
    {
        if ($code === '' || ! preg_match('/^(\d+)_/', $code, $m)) {
            return null;
        }

        $id = (int) $m[1];

        return $id >= 1 ? $id : null;
    }

    public static function nameSegmentFromTitle(string $rawName): string
    {
        return TitleLetterCompactSegment::fromRawTitle($rawName);
    }
}
