<?php

namespace App\Support\Catalog;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * JSON for public verification when outbound mail is unavailable or send fails.
 * Masks detail in production (no debug); logs the real reason server-side.
 */
final class CatalogVerifyMailError
{
    public static function json(string $reason): JsonResponse
    {
        if ($reason === 'mail_not_configured') {
            Log::warning('catalog.collaborators.request_verification_code: outgoing mail not configured');
        }
        // send_failed: verification service already logs with exception context.

        $maskDetail = config('app.env') === 'production' && ! config('app.debug');
        $message = $maskDetail
            ? __('app.collaborator.verify_service_unavailable')
            : ($reason === 'mail_not_configured'
                ? __('app.collaborator.verify_mail_not_configured')
                : __('app.collaborator.verify_mail_send_failed'));

        return response()->json(['message' => $message], 422);
    }
}
