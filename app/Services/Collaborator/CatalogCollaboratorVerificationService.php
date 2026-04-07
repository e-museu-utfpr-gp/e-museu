<?php

namespace App\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Mail\CollaboratorVerificationCodeMail;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Support\Mail\EmailVerificationCode;
use App\Support\Mail\OutgoingMailIsConfigured;

/**
 * Public catalog: request / confirm e-mail verification code (session + mail).
 *
 * Intentionally not `final`: feature tests mock this class via the container (`$this->mock(...)`)
 * for HTTP-layer checks.
 */
class CatalogCollaboratorVerificationService
{
    public function __construct(
        private readonly CollaboratorService $collaborators,
    ) {
    }

    /**
     * @return array{status: 'sent'}|array{status: 'blocked'}|array{status: 'internal_reserved'}
     *         |array{status: 'mail_not_configured'}|array{status: 'send_failed'}
     */
    public function requestEmailVerificationCode(string $email, string $fullName): array
    {
        $email = trim($email);
        $fullName = trim($fullName);

        $existingAnyRole = Collaborator::query()->where('email', $email)->first();
        if ($existingAnyRole !== null && $existingAnyRole->role === CollaboratorRole::INTERNAL) {
            return ['status' => 'internal_reserved'];
        }

        try {
            $collaborator = null;

            if ($existingAnyRole !== null && $existingAnyRole->role === CollaboratorRole::EXTERNAL) {
                if ($existingAnyRole->blocked) {
                    return ['status' => 'blocked'];
                }
                $collaborator = $existingAnyRole;
            }

            if (! OutgoingMailIsConfigured::forDefaultMailer()) {
                return ['status' => 'mail_not_configured'];
            }

            $this->collaborators->clearPublicContributionSessionAuth();

            $generated = EmailVerificationCode::generate();
            $code = $generated['code'];

            session()->put(CollaboratorService::PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY, [
                'email' => mb_strtolower($email),
                'full_name' => $fullName,
                'code_hash' => $generated['hash'],
                'expires_at' => now()->addMinutes(EmailVerificationCode::TTL_MINUTES)->getTimestamp(),
            ]);

            $collaboratorForMail = $collaborator !== null ? ($collaborator->fresh() ?? $collaborator) : null;
            Mail::to($email)->send(new CollaboratorVerificationCodeMail(
                $collaboratorForMail,
                $code,
                app()->getLocale(),
                $fullName,
            ));

            return ['status' => 'sent'];
        } catch (\Throwable $e) {
            Log::error('Public collaborator verification mail failed', [
                'email' => $email,
                'exception' => $e,
            ]);
            session()->forget(CollaboratorService::PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY);

            return ['status' => 'send_failed'];
        }
    }

    /**
     * @return array{status: 'confirmed', collaborator_id: int}|array{status: 'invalid'}
     *         |array{status: 'expired'}|array{status: 'not_found'}
     */
    public function confirmEmailVerificationCode(string $email, string $code, string $fullName): array
    {
        $email = trim($email);
        $code = preg_replace('/\s+/', '', $code) ?? '';
        $fullName = trim($fullName);
        $emailKey = mb_strtolower($email);

        $pendingOutcome = $this->validatePendingAndForget($emailKey, $code);
        if ($pendingOutcome !== null) {
            return $pendingOutcome;
        }

        if ($this->internalExistsForEmail($email)) {
            return ['status' => 'not_found'];
        }

        $collaboratorOrError = $this->resolveExternalCollaborator($email, $fullName);
        if (is_array($collaboratorOrError)) {
            return $collaboratorOrError;
        }

        $collaboratorOrError->forceFill([
            'last_email_verification_at' => now(),
        ])->save();

        $this->collaborators->markPublicContributionSessionAuthenticated($email);

        return ['status' => 'confirmed', 'collaborator_id' => $collaboratorOrError->id];
    }

    /**
     * @return array{status: 'invalid'}|array{status: 'expired'}|null  null when pending OK and cleared
     */
    private function validatePendingAndForget(string $emailKey, string $code): ?array
    {
        $pending = session(CollaboratorService::PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY);
        if (
            ! is_array($pending)
            || ! isset($pending['email'], $pending['code_hash'], $pending['expires_at'])
            || ! hash_equals((string) $pending['email'], $emailKey)
        ) {
            return ['status' => 'invalid'];
        }

        if (now()->getTimestamp() > (int) $pending['expires_at']) {
            session()->forget(CollaboratorService::PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY);

            return ['status' => 'expired'];
        }

        $hash = EmailVerificationCode::hashPlainCode($code);
        if (! hash_equals((string) $pending['code_hash'], $hash)) {
            return ['status' => 'invalid'];
        }

        session()->forget(CollaboratorService::PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY);

        return null;
    }

    private function internalExistsForEmail(string $email): bool
    {
        return Collaborator::query()
            ->where('email', $email)
            ->where('role', CollaboratorRole::INTERNAL)
            ->exists();
    }

    /**
     * @return Collaborator|array{status: 'invalid'}
     */
    private function resolveExternalCollaborator(string $email, string $fullName): Collaborator|array
    {
        $collaborator = Collaborator::query()
            ->where('email', $email)
            ->where('role', CollaboratorRole::EXTERNAL)
            ->first();

        if ($collaborator !== null) {
            return $collaborator;
        }

        if ($fullName === '') {
            return ['status' => 'invalid'];
        }

        try {
            return Collaborator::create([
                'email' => $email,
                'full_name' => $fullName,
                'role' => CollaboratorRole::EXTERNAL,
                'blocked' => false,
            ]);
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) !== 1062) {
                throw $e;
            }
            $collaborator = Collaborator::query()
                ->where('email', $email)
                ->where('role', CollaboratorRole::EXTERNAL)
                ->first();

            return $collaborator ?? ['status' => 'invalid'];
        }
    }
}
