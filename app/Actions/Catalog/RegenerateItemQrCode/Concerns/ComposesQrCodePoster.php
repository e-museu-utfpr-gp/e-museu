<?php

declare(strict_types=1);

namespace App\Actions\Catalog\RegenerateItemQrCode\Concerns;

use GdImage;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * GD: printable QR poster (code + name, QR block, partner logos).
 */
trait ComposesQrCodePoster
{
    private const int POSTER_PADDING = 20;

    private const int POSTER_LOGO_ROW_MAX_HEIGHT = 52;

    private const int POSTER_LOGO_GAP = 40;

    private const int POSTER_SECTION_GAP = 16;

    private const int POSTER_TITLE_NAME_GAP = 6;

    private const int POSTER_TEXT_FONT = 5;

    private const int POSTER_PIECE_NAME_MAX_CHARS = 50;

    /** @var list<string> */
    private const array POSTER_LOGO_PATHS_RELATIVE = [
        'img/qrcode/logo-utfpr-qrcode.png',
        'img/qrcode/logo-unicentro-qrcode.png',
        'img/qrcode/tecnolixo-logo-qrcode.png',
    ];

    protected function composeQrCodePoster(
        string $qrPngBinary,
        string $identificationCode,
        string $pieceName = '',
    ): string {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('GD extension is required to compose QRCode images.');
        }

        $qr = $this->gdImageFromString($qrPngBinary);

        try {
            return $this->composeWithQrResource($qr, $identificationCode, $pieceName);
        } finally {
            imagedestroy($qr);
        }
    }

    private function composeWithQrResource(
        GdImage $qr,
        string $identificationCode,
        string $pieceName,
    ): string {
        $qrW = imagesx($qr);
        $qrH = imagesy($qr);

        $logoSources = $this->loadPosterLogos();
        try {
            $logoMetrics = $this->measurePosterLogoRow($logoSources);
            $layout = $this->posterHeaderTextLayout($identificationCode, $pieceName);
            [$canvasW, $canvasH] = $this->posterCanvasDimensions($qrW, $qrH, $logoMetrics, $layout);

            ['canvas' => $canvas, 'ink' => $ink] = $this->newWhitePosterCanvas($canvasW, $canvasH);

            try {
                $y = $this->drawCenteredCodeAndOptionalName($canvas, $layout, $ink, self::POSTER_PADDING);
                $y += self::POSTER_SECTION_GAP;
                $y = $this->copyQrCenteredX($canvas, $qr, $qrW, $qrH, $y);
                $y += self::POSTER_SECTION_GAP;
                $this->resampleLogosOntoCanvas($canvas, $logoMetrics, $y, $logoSources);

                return $this->encodeCanvasAsPng($canvas);
            } finally {
                imagedestroy($canvas);
            }
        } finally {
            foreach ($logoSources as $im) {
                imagedestroy($im);
            }
        }
    }

    /**
     * @param array{
     *     totalWidth: int,
     *     rowHeight: int,
     *     entries: list<array{targetW: int, targetH: int, srcW: int, srcH: int}>
     * } $logoMetrics
     * @param array{
     *     codeW: int,
     *     nameW: int,
     *     headerH: int
     * } $layout
     *
     * @return array{0: int, 1: int}
     */
    private function posterCanvasDimensions(int $qrW, int $qrH, array $logoMetrics, array $layout): array
    {
        $innerW = max($qrW, $logoMetrics['totalWidth'], $layout['codeW'], $layout['nameW']);
        $canvasW = max(1, $innerW + 2 * self::POSTER_PADDING);
        $logoRowHeight = $logoMetrics['rowHeight'] > 0 ? $logoMetrics['rowHeight'] : 0;
        $canvasH = max(
            1,
            self::POSTER_PADDING
                + $layout['headerH']
                + self::POSTER_SECTION_GAP
                + $qrH
                + self::POSTER_SECTION_GAP
                + $logoRowHeight
                + self::POSTER_PADDING,
        );

        return [$canvasW, $canvasH];
    }

    /**
     * @return array{
     *     codeLine: string,
     *     nameLine: string,
     *     codeW: int,
     *     nameW: int,
     *     headerH: int,
     *     textH: int
     * }
     */
    private function posterHeaderTextLayout(string $identificationCode, string $pieceName): array
    {
        $codeLabel = trim($identificationCode);
        if ($codeLabel === '') {
            $codeLabel = '—';
        }

        $codeLine = $this->toSingleByteForGdFont($codeLabel);
        $nameTruncated = $this->truncatePieceNameUtf8(trim($pieceName));
        $nameLine = $nameTruncated !== '' ? $this->toSingleByteForGdFont($nameTruncated) : '';

        $fontW = imagefontwidth(self::POSTER_TEXT_FONT);
        $textH = imagefontheight(self::POSTER_TEXT_FONT);
        $codeW = $fontW * strlen($codeLine);
        $nameW = $nameLine !== '' ? $fontW * strlen($nameLine) : 0;

        $headerH = $textH;
        if ($nameLine !== '') {
            $headerH += self::POSTER_TITLE_NAME_GAP + $textH;
        }

        return [
            'codeLine' => $codeLine,
            'nameLine' => $nameLine,
            'codeW' => $codeW,
            'nameW' => $nameW,
            'headerH' => $headerH,
            'textH' => $textH,
        ];
    }

    /**
     * @return array{canvas: GdImage, ink: int}
     */
    private function newWhitePosterCanvas(int $canvasW, int $canvasH): array
    {
        $canvasW = max(1, $canvasW);
        $canvasH = max(1, $canvasH);
        $canvas = imagecreatetruecolor($canvasW, $canvasH);
        if ($canvas === false) {
            throw new RuntimeException('Could not allocate image canvas.');
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $ink = imagecolorallocate($canvas, 13, 35, 61);
        if ($white === false || $ink === false) {
            imagedestroy($canvas);

            throw new RuntimeException('Could not allocate image colors.');
        }

        imagefilledrectangle($canvas, 0, 0, $canvasW, $canvasH, $white);
        imagealphablending($canvas, true);

        return ['canvas' => $canvas, 'ink' => $ink];
    }

    /**
     * @param array{
     *     codeLine: string,
     *     nameLine: string,
     *     codeW: int,
     *     nameW: int,
     *     textH: int
     * } $layout
     */
    private function drawCenteredCodeAndOptionalName(GdImage $canvas, array $layout, int $ink, int $y): int
    {
        $canvasW = imagesx($canvas);
        $textX = (int) (($canvasW - $layout['codeW']) / 2);
        imagestring($canvas, self::POSTER_TEXT_FONT, $textX, $y, $layout['codeLine'], $ink);
        $y += $layout['textH'];
        if ($layout['nameLine'] === '') {
            return $y;
        }

        $y += self::POSTER_TITLE_NAME_GAP;
        $nx = (int) (($canvasW - $layout['nameW']) / 2);
        imagestring($canvas, self::POSTER_TEXT_FONT, $nx, $y, $layout['nameLine'], $ink);

        return $y + $layout['textH'];
    }

    private function copyQrCenteredX(GdImage $canvas, GdImage $qr, int $qrW, int $qrH, int $y): int
    {
        $canvasW = imagesx($canvas);
        $qrX = (int) (($canvasW - $qrW) / 2);
        imagecopy($canvas, $qr, $qrX, $y, 0, 0, $qrW, $qrH);

        return $y + $qrH;
    }

    /**
     * @param array{
     *     entries: list<array{targetW: int, targetH: int, srcW: int, srcH: int}>,
     *     totalWidth: int,
     *     rowHeight: int
     * } $logoMetrics
     * @param list<GdImage> $sources
     */
    private function resampleLogosOntoCanvas(
        GdImage $canvas,
        array $logoMetrics,
        int $y,
        array $sources,
    ): void {
        if ($logoMetrics['entries'] === []) {
            return;
        }

        $canvasW = imagesx($canvas);
        $x = (int) (($canvasW - $logoMetrics['totalWidth']) / 2);
        $yLogo = $y + (int) (($logoMetrics['rowHeight'] - self::POSTER_LOGO_ROW_MAX_HEIGHT) / 2);
        foreach ($logoMetrics['entries'] as $idx => $entry) {
            $src = $sources[$idx] ?? null;
            if (! $src instanceof GdImage) {
                continue;
            }
            imagecopyresampled(
                $canvas,
                $src,
                $x,
                $yLogo,
                0,
                0,
                $entry['targetW'],
                $entry['targetH'],
                $entry['srcW'],
                $entry['srcH'],
            );
            $x += $entry['targetW'] + self::POSTER_LOGO_GAP;
        }
    }

    private function encodeCanvasAsPng(GdImage $canvas): string
    {
        ob_start();
        imagepng($canvas);
        $binary = ob_get_clean();

        if (! is_string($binary) || $binary === '') {
            throw new RuntimeException('Failed to encode composed QRCode PNG.');
        }

        return $binary;
    }

    private function gdImageFromString(string $binary): GdImage
    {
        $qr = self::withGdWarningsSuppressed(static fn () => imagecreatefromstring($binary));
        if (! $qr instanceof GdImage) {
            throw new RuntimeException('QRCode response was not a valid PNG image.');
        }

        return $qr;
    }

    /**
     * @template T
     *
     * @param callable(): T $callback
     *
     * @return T
     */
    private static function withGdWarningsSuppressed(callable $callback): mixed
    {
        set_error_handler(static function (int $errno): bool {
            return in_array($errno, [
                E_WARNING,
                E_NOTICE,
                E_USER_WARNING,
                E_USER_NOTICE,
                E_DEPRECATED,
                E_USER_DEPRECATED,
            ], true);
        });
        try {
            return $callback();
        } finally {
            restore_error_handler();
        }
    }

    private function truncatePieceNameUtf8(string $name): string
    {
        if ($name === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($name, 'UTF-8') <= self::POSTER_PIECE_NAME_MAX_CHARS) {
                return $name;
            }

            return mb_substr($name, 0, self::POSTER_PIECE_NAME_MAX_CHARS, 'UTF-8') . '...';
        }

        if (strlen($name) <= self::POSTER_PIECE_NAME_MAX_CHARS) {
            return $name;
        }

        return substr($name, 0, self::POSTER_PIECE_NAME_MAX_CHARS) . '...';
    }

    private function toSingleByteForGdFont(string $utf8): string
    {
        if ($utf8 === '') {
            return '';
        }

        $latin = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $utf8);
        if ($latin === false) {
            $latin = iconv('UTF-8', 'ISO-8859-1//IGNORE', $utf8);
        }

        return $latin !== false && $latin !== '' ? $latin : $utf8;
    }

    /**
     * @return list<GdImage>
     */
    private function loadPosterLogos(): array
    {
        $out = [];
        foreach (self::POSTER_LOGO_PATHS_RELATIVE as $relative) {
            $path = public_path($relative);
            if (! is_file($path)) {
                Log::warning('QR poster partner logo file is missing.', ['relative_path' => $relative]);

                continue;
            }
            $probe = getimagesize($path);
            if ($probe === false || $probe[0] < 1 || $probe[1] < 1) {
                Log::warning('QR poster partner logo has missing or invalid dimensions.', [
                    'relative_path' => $relative,
                ]);

                continue;
            }
            $im = self::withGdWarningsSuppressed(static fn () => imagecreatefrompng($path));
            if (! $im instanceof GdImage) {
                Log::warning('QR poster partner logo is not a valid PNG.', ['relative_path' => $relative]);

                continue;
            }
            imagealphablending($im, true);
            imagesavealpha($im, true);
            $out[] = $im;
        }

        return $out;
    }

    /**
     * @param list<GdImage> $logos
     *
     * @return array{
     *     entries: list<array{targetW: int, targetH: int, srcW: int, srcH: int}>,
     *     totalWidth: int,
     *     rowHeight: int
     * }
     */
    private function measurePosterLogoRow(array $logos): array
    {
        if ($logos === []) {
            return ['entries' => [], 'totalWidth' => 0, 'rowHeight' => 0];
        }

        $entries = [];
        $totalWidth = 0;
        foreach ($logos as $im) {
            $rawW = imagesx($im);
            $rawH = imagesy($im);
            $targetH = self::POSTER_LOGO_ROW_MAX_HEIGHT;
            $targetW = (int) max(1, round($rawW * ($targetH / $rawH)));
            $entries[] = [
                'targetW' => $targetW,
                'targetH' => $targetH,
                'srcW' => $rawW,
                'srcH' => $rawH,
            ];
            $totalWidth += $targetW;
        }

        $totalWidth += self::POSTER_LOGO_GAP * (count($entries) - 1);

        return [
            'entries' => $entries,
            'totalWidth' => $totalWidth,
            'rowHeight' => self::POSTER_LOGO_ROW_MAX_HEIGHT,
        ];
    }
}
