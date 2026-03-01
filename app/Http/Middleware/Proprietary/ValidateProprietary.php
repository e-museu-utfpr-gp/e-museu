<?php

namespace App\Http\Middleware\Proprietary;

use App\Models\Proprietary\Proprietary;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateProprietary
{
    public function handle(Request $request, Closure $next): Response
    {
        $proprietaryContact = $request->input('contact');

        if ($proprietaryContact) {
            $proprietary = Proprietary::where('contact', $proprietaryContact)->first();

            if ($proprietary && $proprietary->is_admin) {
                abort(403, __('app.proprietary.access_denied'));
            }
        }

        return $next($request);
    }
}
