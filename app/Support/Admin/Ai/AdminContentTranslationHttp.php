<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * JSON responses and structured logging for {@see \App\Http\Controllers\Admin\Ai\AdminContentTranslationController}.
 */
final class AdminContentTranslationHttp
{
    public static function blockedTranslationResponse(): ?JsonResponse
    {
        return match (AdminAi::translationEndpointBlockReason()) {
            'disabled' => response()->json(['message' => (string) __('view.admin.ai.disabled')], 503),
            'not_configured' => response()->json(['message' => (string) __('view.admin.ai.not_configured')], 503),
            default => null,
        };
    }

    public static function forcedProviderUnavailableResponse(?string $forcedProvider): ?JsonResponse
    {
        if ($forcedProvider === null || in_array($forcedProvider, AdminAi::configuredProviderSlugs(), true)) {
            return null;
        }

        return response()->json([
            'message' => (string) __('view.admin.ai.error_selected_provider_unavailable', [
                'provider' => AdminAi::providerLabel($forcedProvider),
            ]),
        ], 422);
    }

    public static function logSuccess(
        Request $request,
        string $resource,
        string $targetLocale,
        string $mode,
        string $requestedProvider,
        ?string $provider,
        ?string $providerLabel,
        ?string $model,
    ): void {
        Log::info('admin.ai.translation', [
            'outcome' => 'success',
            'admin_id' => $request->user()?->getAuthIdentifier(),
            'resource' => $resource,
            'target_locale' => $targetLocale,
            'mode' => $mode,
            'requested_provider' => $requestedProvider,
            'provider' => $provider,
            'provider_label' => $providerLabel,
            'model' => $model,
        ]);
    }

    public static function logUserError(
        Request $request,
        string $resource,
        string $targetLocale,
        string $mode,
        string $requestedProvider,
        ?string $provider,
        ?string $model,
        string $reasonKey,
        ?string $previousReasonKey,
    ): void {
        Log::info('admin.ai.translation', [
            'outcome' => 'user_error',
            'admin_id' => $request->user()?->getAuthIdentifier(),
            'resource' => $resource,
            'target_locale' => $targetLocale,
            'mode' => $mode,
            'requested_provider' => $requestedProvider,
            'provider' => $provider,
            'model' => $model,
            'reason_key' => $reasonKey,
            'previous_reason_key' => $previousReasonKey,
        ]);
    }

    public static function logProviderError(
        Request $request,
        string $resource,
        string $targetLocale,
        string $mode,
        string $requestedProvider,
        ?string $provider,
        ?string $model,
        Throwable $throwable,
    ): void {
        Log::error('admin.ai.translation', [
            'outcome' => 'provider_error',
            'admin_id' => $request->user()?->getAuthIdentifier(),
            'resource' => $resource,
            'target_locale' => $targetLocale,
            'mode' => $mode,
            'requested_provider' => $requestedProvider,
            'provider' => $provider,
            'model' => $model,
            'exception_class' => $throwable::class,
            'exception_message' => $throwable->getMessage(),
            'exception_file' => $throwable->getFile(),
            'exception_line' => $throwable->getLine(),
        ]);
    }
}
