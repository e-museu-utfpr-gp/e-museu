<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

final class DeployWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->acceptsBearerSecret($request)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $deployUrl = config('deploy.coolify.deploy_url');
        $coolifyToken = config('deploy.coolify.token');

        if (! is_string($deployUrl) || $deployUrl === '' || ! is_string($coolifyToken) || $coolifyToken === '') {
            abort(Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            $coolifyResponse = Http::timeout(60)
                ->withToken($coolifyToken)
                ->acceptJson()
                ->get($deployUrl);
        } catch (ConnectionException $e) {
            report($e);

            return response()->json(['message' => 'Coolify unreachable.'], Response::HTTP_BAD_GATEWAY);
        }

        if (! $coolifyResponse->successful()) {
            return response()->json(['message' => 'Coolify deploy request failed.'], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['ok' => true]);
    }

    private function acceptsBearerSecret(Request $request): bool
    {
        $secret = config('deploy.secret');

        if (! is_string($secret) || $secret === '') {
            return false;
        }

        $bearer = $request->bearerToken();

        if ($bearer === null || $bearer === '') {
            return false;
        }

        return hash_equals($secret, $bearer);
    }
}
