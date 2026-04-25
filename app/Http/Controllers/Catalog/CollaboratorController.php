<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CatalogCollaboratorVerificationService;
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\CatalogVerifyMailError;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Validator;

class CollaboratorController extends Controller
{
    /**
     * Look up collaborator contact state for the contribution forms.
     *
     * Shared by two routes — keep the JSON contract stable for both:
     * - Public: `catalog.collaborators.check-contact` (throttle `collaborator-check-contact`).
     * - Admin: `admin.catalog.collaborators.check-contact` (authenticate + throttle `web-admin`).
     *
     * JSON contract:
     * - `email_verified` (bool|null): external record verified; null when `skip_contact_check` is true
     *   (no lookup — not “verified”).
     * - `skip_contact_check` (bool, optional): when true, do not treat `email_verified` as a DB fact
     *   (empty input or internal-reserved branch).
     */
    public function checkContact(Request $request, CollaboratorService $collaboratorService): JsonResponse
    {
        $this->validateCheckContact($request);
        $verificationEnabled = (bool) config('mail.public_contribution_email_verification_enabled');

        $email = trim((string) $request->input('email', ''));
        if ($email === '') {
            return response()->json($this->checkContactEmptyEmailPayload());
        }

        if ($this->isInternalReservedEmail($email)) {
            return response()->json($this->checkContactInternalReservedPayload());
        }

        $collaborator = $collaboratorService->findExternalByEmail($email);
        $submittedName = trim((string) $request->input('full_name', ''));
        $nameDiffersFromRecord = $collaborator !== null
            && $submittedName !== ''
            && ! $collaboratorService->submittedCollaboratorNameMatchesRecord(
                (string) $collaborator->full_name,
                $submittedName,
            );

        return response()->json([
            'exists' => $collaborator !== null,
            'internal_reserved' => false,
            'full_name' => $collaborator !== null ? (string) $collaborator->full_name : '',
            'email_verified' => ! $verificationEnabled || $collaborator === null || $collaborator->hasVerifiedEmail(),
            'contribution_session_verified' => ! $verificationEnabled
                || $collaboratorService->publicContributionSessionIsAuthenticatedForEmail($email),
            'name_differs_from_record' => $nameDiffersFromRecord,
            'collaborator_id' => $collaborator?->id,
        ]);
    }

    public function clearContributionSession(CollaboratorService $collaboratorService): JsonResponse
    {
        $collaboratorService->clearPublicContributionSessionAuth();

        return response()->json(['ok' => true]);
    }

    /**
     * Sends the verification e-mail only after {@see \App\Http\Middleware\VerifyAntiBotChallenge} (scope
     * `verification-request`) accepts a valid anti-bot token; the mailer is not called when that fails.
     */
    public function requestVerificationCode(
        Request $request,
        CatalogCollaboratorVerificationService $verification,
    ): JsonResponse {
        abort_unless((bool) config('mail.public_contribution_email_verification_enabled'), 404);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:200'],
            'full_name' => ['required', 'string', 'min:1', 'max:200'],
        ]);

        $result = $verification->requestEmailVerificationCode(
            $validated['email'],
            $validated['full_name'],
        );

        return match ($result['status']) {
            'sent' => response()->json([
                'message' => __('app.collaborator.verify_code_sent'),
            ]),
            'blocked' => response()->json(['message' => __('app.collaborator.verify_blocked')], 403),
            'internal_reserved' => response()->json(
                ['message' => __('app.collaborator.verify_internal_reserved')],
                422,
            ),
            'mail_not_configured', 'send_failed' => CatalogVerifyMailError::json($result['status']),
        };
    }

    public function confirmVerificationCode(
        Request $request,
        CatalogCollaboratorVerificationService $verification,
    ): JsonResponse {
        abort_unless((bool) config('mail.public_contribution_email_verification_enabled'), 404);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:200'],
            'code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'full_name' => ['required', 'string', 'min:1', 'max:200'],
        ]);

        $result = $verification->confirmEmailVerificationCode(
            $validated['email'],
            $validated['code'],
            $validated['full_name'],
        );

        return match ($result['status']) {
            'confirmed' => response()->json([
                'message' => __('app.collaborator.verify_confirmed'),
                'collaborator_id' => (int) $result['collaborator_id'],
            ]),
            'expired' => response()->json(['message' => __('app.collaborator.verify_code_expired')], 422),
            'not_found', 'invalid' => response()->json(['message' => __('app.collaborator.verify_invalid_code')], 422),
        };
    }

    private function validateCheckContact(Request $request): void
    {
        $request->validate([
            'email' => [
                'nullable',
                'string',
                'max:200',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }
                    if (trim($value) === '') {
                        return;
                    }
                    $validator = Validator::make(
                        ['email' => trim($value)],
                        ['email' => 'email:rfc'],
                    );
                    if ($validator->fails()) {
                        $fail($validator->errors()->first('email'));
                    }
                },
            ],
            'full_name' => 'nullable|string|max:200',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function checkContactEmptyEmailPayload(): array
    {
        return [
            'exists' => false,
            'internal_reserved' => false,
            'full_name' => '',
            'skip_contact_check' => true,
            'email_verified' => null,
            'contribution_session_verified' => false,
            'name_differs_from_record' => false,
            'collaborator_id' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkContactInternalReservedPayload(): array
    {
        return [
            'exists' => false,
            'internal_reserved' => true,
            'skip_contact_check' => true,
            'email_verified' => null,
            'contribution_session_verified' => false,
            'name_differs_from_record' => false,
            'collaborator_id' => null,
        ];
    }

    private function isInternalReservedEmail(string $email): bool
    {
        return Collaborator::query()
            ->where('email', '=', $email)
            ->where('role', CollaboratorRole::INTERNAL)
            ->exists();
    }
}
