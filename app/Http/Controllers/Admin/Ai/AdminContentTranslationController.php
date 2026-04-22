<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Ai;

use App\Http\Controllers\Controller;
use App\Actions\Admin\Ai\AdminChatCompletionAction\AdminChatCompletionAction;
use App\Http\Requests\Admin\Ai\AdminContentTranslationRequest;
use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

final class AdminContentTranslationController extends Controller
{
    public function translate(
        AdminContentTranslationRequest $request,
        AdminChatCompletionAction $adminChatCompletion,
    ): JsonResponse {
        $block = AdminAi::translationEndpointBlockReason();
        if ($block === 'disabled') {
            return response()->json([
                'message' => (string) __('view.admin.ai.disabled'),
            ], 503);
        }

        if ($block === 'not_configured') {
            return response()->json([
                'message' => (string) __('view.admin.ai.not_configured'),
            ], 503);
        }

        $resource = (string) $request->validated('resource');
        $targetLocale = (string) $request->validated('target_locale');
        $mode = (string) $request->validated('mode');
        $translations = $request->validatedTranslationsPayload();

        try {
            $payload = $adminChatCompletion->translateContent($resource, $targetLocale, $mode, $translations);

            Log::info('admin.ai.translation', [
                'outcome' => 'success',
                'admin_id' => $request->user()?->getAuthIdentifier(),
                'resource' => $resource,
                'target_locale' => $targetLocale,
                'mode' => $mode,
            ]);

            return response()->json(['translations' => $payload]);
        } catch (AiTranslationUserException $e) {
            Log::info('admin.ai.translation', [
                'outcome' => 'user_error',
                'admin_id' => $request->user()?->getAuthIdentifier(),
                'resource' => $resource,
                'target_locale' => $targetLocale,
                'mode' => $mode,
                'reason_key' => $e->translationKey,
            ]);

            return response()->json([
                'message' => (string) __($e->translationKey, $e->translationReplace),
            ], 422);
        } catch (Throwable $e) {
            Log::error('admin.ai.translation', [
                'outcome' => 'provider_error',
                'admin_id' => $request->user()?->getAuthIdentifier(),
                'resource' => $resource,
                'target_locale' => $targetLocale,
                'mode' => $mode,
                'exception_class' => $e::class,
                'exception_message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'message' => (string) __('view.admin.ai.provider_error'),
            ], 502);
        }
    }
}
