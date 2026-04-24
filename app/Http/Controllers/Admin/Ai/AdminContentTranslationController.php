<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Actions\Admin\Ai\AdminChatCompletion\AdminChatCompletionAction;
use App\Http\Requests\Admin\Ai\AdminContentTranslationRequest;
use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;
use App\Support\Admin\Ai\AdminContentTranslationHttp;
use Illuminate\Http\JsonResponse;
use Throwable;

final class AdminContentTranslationController extends Controller
{
    public function translate(
        AdminContentTranslationRequest $request,
        AdminChatCompletionAction $adminChatCompletion,
    ): JsonResponse {
        $blocked = AdminContentTranslationHttp::blockedTranslationResponse();
        if ($blocked !== null) {
            return $blocked;
        }

        $resource = (string) $request->validated('resource');
        $targetLocale = (string) $request->validated('target_locale');
        $mode = (string) $request->validated('mode');
        $requestedProvider = (string) ($request->validated('provider') ?? 'auto');
        $forcedProvider = $requestedProvider !== 'auto' ? $requestedProvider : null;
        $translations = $request->validatedTranslationsPayload();
        $provider = null;
        $model = null;
        $providerLabel = null;

        $providerUnavailable = AdminContentTranslationHttp::forcedProviderUnavailableResponse($forcedProvider);
        if ($providerUnavailable !== null) {
            return $providerUnavailable;
        }

        try {
            $translation = $adminChatCompletion->translateContent(
                $resource,
                $targetLocale,
                $mode,
                $translations,
                $forcedProvider,
            );
            $provider = $translation['provider'];
            $model = $translation['model'];
            $providerLabel = AdminAi::providerLabel($provider);

            AdminContentTranslationHttp::logSuccess(
                $request,
                $resource,
                $targetLocale,
                $mode,
                $requestedProvider,
                $provider,
                $providerLabel,
                $model,
            );

            return response()->json([
                'translations' => $translation['translations'],
                'provider' => $translation['provider'],
                'provider_label' => $providerLabel,
                'model' => $translation['model'],
                'requested_provider' => $requestedProvider,
            ]);
        } catch (AiTranslationUserException $e) {
            $previousReasonKey = $e->getPrevious() instanceof AiTranslationUserException
                ? $e->getPrevious()->translationKey
                : null;
            AdminContentTranslationHttp::logUserError(
                $request,
                $resource,
                $targetLocale,
                $mode,
                $requestedProvider,
                $provider,
                $model,
                $e->translationKey,
                $previousReasonKey,
            );

            return response()->json([
                'message' => (string) __($e->translationKey, $e->translationReplace),
            ], 422);
        } catch (Throwable $throwable) {
            AdminContentTranslationHttp::logProviderError(
                $request,
                $resource,
                $targetLocale,
                $mode,
                $requestedProvider,
                $provider,
                $model,
                $throwable,
            );

            return response()->json([
                'message' => (string) __('view.admin.ai.internal_error'),
            ], 500);
        }
    }
}
