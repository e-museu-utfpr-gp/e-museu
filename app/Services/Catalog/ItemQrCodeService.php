<?php

namespace App\Services\Catalog;

use App\Enums\Catalog\ItemImageType;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ItemQrCodeService
{
    public function qrCodeImageForItem(Item $item): ?ItemImage
    {
        /** @var ItemImage|null $img */
        $img = $item->images()->where('type', ItemImageType::QRCODE->value)->first();

        return $img;
    }

    public function destinationUrlForItem(Item $item): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        return sprintf(
            '%s/codes/%s',
            $baseUrl,
            rawurlencode((string) $item->identification_code),
        );
    }

    public function targetUrlFromQrImage(?ItemImage $image): ?string
    {
        if (! $image instanceof ItemImage) {
            return null;
        }
        $base = basename((string) $image->getRawOriginal('path'));
        if ($base === '') {
            return null;
        }
        $dotPos = strrpos($base, '.');
        $encoded = $dotPos === false ? $base : substr($base, 0, $dotPos);
        if ($encoded === '') {
            return null;
        }

        $padding = (4 - (strlen($encoded) % 4)) % 4;
        $padded = strtr($encoded, '-_', '+/') . str_repeat('=', $padding);
        $decoded = base64_decode($padded, true);
        if (! is_string($decoded) || trim($decoded) === '') {
            return null;
        }

        if (! filter_var($decoded, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($decoded, PHP_URL_SCHEME));
        if ($scheme !== 'http' && $scheme !== 'https') {
            return null;
        }

        return $decoded;
    }

    public function isQrDomainCompatible(?string $targetUrl): bool
    {
        if ($targetUrl === null || trim($targetUrl) === '') {
            return true;
        }

        return $this->normalizeOriginForCompare($targetUrl)
            === $this->normalizeOriginForCompare((string) config('app.url'));
    }

    public function regenerateForItem(Item $item): ItemImage
    {
        $this->deleteForItem($item);

        $targetUrl = $this->destinationUrlForItem($item);
        $response = Http::timeout(20)
            ->retry(2, 200)
            ->get('https://api.qrserver.com/v1/create-qr-code/', [
                'size' => '512x512',
                'format' => 'png',
                'margin' => '0',
                'data' => $targetUrl,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to generate QRCode image.');
        }

        $path = ItemImage::buildQrCodePath($item, $targetUrl, 'png');
        if (! Storage::disk('public')->put($path, $response->body())) {
            throw new RuntimeException(__('app.catalog.item.upload_store_failed'));
        }

        /** @var ItemImage $img */
        $img = $item->images()->create([
            'path' => $path,
            'type' => ItemImageType::QRCODE,
            'sort_order' => 0,
        ]);

        return $img;
    }

    private function normalizeOriginForCompare(string $url): string
    {
        $parts = parse_url($url);
        if (! is_array($parts)) {
            return '';
        }
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        $host = preg_replace('/^www\./', '', $host) ?? $host;
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return $scheme . '://' . $host . $port;
    }

    public function deleteForItem(Item $item): void
    {
        /** @var \Illuminate\Support\Collection<int, ItemImage> $qrImages */
        $qrImages = $item->images()->where('type', ItemImageType::QRCODE->value)->get();
        foreach ($qrImages as $img) {
            $path = $img->getRawOriginal('path');
            if ($path !== null && $path !== '' && ! str_starts_with((string) $path, 'http')) {
                Storage::disk('public')->delete($path);
            }
            $img->delete();
        }
    }
}
