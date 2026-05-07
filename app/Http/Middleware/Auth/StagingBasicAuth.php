<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StagingBasicAuth
{
    /**
     * Requires HTTP Basic Auth when APP_ENV=staging.
     * Credentials from STAGING_HTTP_USER and STAGING_HTTP_PASSWORD.
     *
     * {@see DeployWebhookController} uses Bearer only; Basic cannot coexist with Bearer on one request,
     * so the Coolify deploy hook path is excluded from this gate (still protected by DEPLOY_HOOK_SECRET).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('staging')) {
            return $next($request);
        }

        if ($request->is('deploy')) {
            return $next($request);
        }

        $user = config('auth.staging_basic.user');
        $password = config('auth.staging_basic.password');

        if (empty($user) || empty($password)) {
            return $next($request);
        }

        if ($request->getUser() !== $user || $request->getPassword() !== $password) {
            return response('Staging: authentication required.', 401, [
                'WWW-Authenticate' => 'Basic realm="Staging", charset="UTF-8"',
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        return $next($request);
    }
}
