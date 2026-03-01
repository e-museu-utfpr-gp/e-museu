<?php

namespace App\Http\Middleware\Catalog;

use App\Models\Catalog\Item;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateItem
{
    public function handle(Request $request, Closure $next): Response
    {
        $itemId = $request->route('id');

        if ($itemId) {
            $item = Item::find($itemId);

            if ($item && $item->validation === false) {
                abort(403, __('app.catalog.item.access_denied'));
            }
        }

        return $next($request);
    }
}
