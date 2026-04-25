<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Item;
use App\Support\Catalog\ItemIdentificationCode;
use Illuminate\Http\RedirectResponse;

final class IdentificationCodeRedirectController extends Controller
{
    public function __invoke(string $code): RedirectResponse
    {
        if (strlen($code) > 255) {
            abort(404);
        }

        $id = ItemIdentificationCode::parseLeadingId($code);
        if ($id === null) {
            abort(404);
        }

        $item = Item::query()
            ->whereKey($id)
            ->where('identification_code', $code)
            ->where('validation', true)
            ->first();

        if ($item === null) {
            abort(404);
        }

        return redirect()->route('catalog.items.show', ['id' => $id], 302);
    }
}
