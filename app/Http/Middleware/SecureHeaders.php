<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // $response->headers->set('Content-Security-Policy', "default-src 'self'");

        // Uncomment the line below to enable HTTPS enforcement
        // $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
