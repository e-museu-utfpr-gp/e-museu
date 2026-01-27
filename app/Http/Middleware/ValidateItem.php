<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Item;
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
                abort(403, 'Acesso negado.');
            }
        }

        return $next($request);
    }
}
