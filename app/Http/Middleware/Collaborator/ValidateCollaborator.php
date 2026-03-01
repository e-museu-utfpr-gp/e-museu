<?php

namespace App\Http\Middleware\Collaborator;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCollaborator
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
