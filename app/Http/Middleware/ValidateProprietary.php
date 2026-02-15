<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Proprietary\Proprietary;

class ValidateProprietary
{
    public function handle(Request $request, Closure $next): Response
    {
        $proprietaryContact = $request->input('contact');

        if ($proprietaryContact) {
            $proprietary = Proprietary::where('contact', $proprietaryContact)->first();

            if ($proprietary && $proprietary->is_admin) {
                abort(403, 'Acesso negado.');
            }
        }

        return $next($request);
    }
}
