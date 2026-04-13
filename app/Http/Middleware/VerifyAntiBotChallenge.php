<?php

namespace App\Http\Middleware;

use App\Support\Security\AntiBotVerifier;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class VerifyAntiBotChallenge
{
    public function __construct(
        private readonly AntiBotVerifier $antiBotVerifier,
    ) {
    }

    /**
     * @param  Closure(Request): Response  $next
     * @param  string|null  $scope  Use `verification-request` for the e-mail verification Turnstile field.
     */
    public function handle(Request $request, Closure $next, ?string $scope = null): Response
    {
        if ($scope !== null && $scope !== 'verification-request') {
            throw new InvalidArgumentException(
                'Unknown antibot middleware scope "' . $scope . '". '
                . 'Omit the parameter for the default response field, or use "verification-request" '
                . 'for the catalog e-mail code request.',
            );
        }

        $override = null;
        if ($scope === 'verification-request') {
            $override = (string) config('antibot.verification_request_response_input');
        }

        $this->antiBotVerifier->validate($request, $override);

        return $next($request);
    }
}
