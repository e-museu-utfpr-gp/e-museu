<?php

namespace App\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Support\Admin\{AdminIndexConfig, AdminIndexQueryBuilder};
use App\Support\Mail\EmailVerificationCode;

class CollaboratorService
{
    /** Session: last successful e-mail code confirm for public contribution (one submission per auth). */
    public const string PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY = 'catalog_public_contribution_auth';

    /** Session: pending e-mail verification code (hash + expiry) for the public catalog flow — not persisted in DB. */
    public const string PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY = 'catalog_email_verification_pending';

    /**
     * @return array{collaborators: LengthAwarePaginator<int, Collaborator>, count: int}
     */
    public function getPaginatedCollaboratorsForAdminIndex(Request $request): array
    {
        $query = Collaborator::query()->with('locks');

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::collaborators());

        $collaborators = $query->paginate(10)->withQueryString();

        return [
            'collaborators' => $collaborators,
            'count' => $collaborators->total(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createCollaborator(array $data): Collaborator
    {
        $collaborator = Collaborator::create($data);

        if ($collaborator->role === CollaboratorRole::INTERNAL && ! $collaborator->hasVerifiedEmail()) {
            $collaborator->forceFill(['last_email_verification_at' => now()])->save();
        }

        return $collaborator->fresh() ?? $collaborator;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateCollaborator(Collaborator $collaborator, array $data): void
    {
        $oldEmail = $collaborator->email;
        $oldRole = $collaborator->role;
        $collaborator->update($data);
        $collaborator->refresh();

        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            if (! $collaborator->hasVerifiedEmail()) {
                $collaborator->forceFill(['last_email_verification_at' => now()])->save();
            }

            return;
        }

        $emailChanged = isset($data['email']) && $data['email'] !== $oldEmail;
        $roleBecameExternal = $oldRole === CollaboratorRole::INTERNAL;
        if (! $emailChanged && ! $roleBecameExternal) {
            return;
        }

        $adminMarkedVerified = array_key_exists('last_email_verification_at', $data)
            && filled($data['last_email_verification_at']);

        if (! $adminMarkedVerified) {
            $collaborator->forceFill(['last_email_verification_at' => null]);
        }

        $collaborator->save();
    }

    public function deleteCollaborator(Collaborator $collaborator): void
    {
        $collaborator->delete();
    }

    /**
     * Exact e-mail lookup (any role). Public item contributions must not insert a row here — creation happens in
     * {@see CatalogCollaboratorVerificationService::confirmEmailVerificationCode} after the code flow.
     */
    public function findCollaboratorByEmailForPublicLookup(string $email): ?Collaborator
    {
        return Collaborator::query()->where('email', '=', $email)->first();
    }

    /**
     * @return Collection<int, Collaborator>
     */
    public function getForForm(): Collection
    {
        return Collaborator::orderBy('full_name')->get();
    }

    public function findExternalByEmail(string $email): ?Collaborator
    {
        $email = trim($email);

        return Collaborator::query()
            ->where('email', '=', $email)
            ->where('role', CollaboratorRole::EXTERNAL)
            ->where('blocked', false)
            ->first();
    }

    /**
     * Normalize full name for comparison (trim, collapse spaces, case-insensitive).
     */
    private function normalizeCollaboratorName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/u', ' ', $name) ?? $name;

        return mb_strtolower($name, 'UTF-8');
    }

    private function collaboratorNamesMatch(string $stored, string $submitted): bool
    {
        return $this->normalizeCollaboratorName($stored) === $this->normalizeCollaboratorName($submitted);
    }

    /**
     * Whether the submitted full name matches the stored one (trim, collapsed spaces, case-insensitive).
     */
    public function submittedCollaboratorNameMatchesRecord(string $storedFullName, string $submittedFullName): bool
    {
        return $this->collaboratorNamesMatch($storedFullName, $submittedFullName);
    }

    /**
     * Public contribution: must have confirmed the e-mail code in this session (each item/extra needs a fresh confirm).
     * Stored {@see Collaborator::last_email_verification_at} is not enough without session auth.
     *
     * @param  array<string, mixed>  $collaboratorData
     * @return 'ok'|'email_unverified'
     */
    public function publicContributionCollaboratorGate(Collaborator $collaborator, array $collaboratorData): string
    {
        $submittedEmail = (string) ($collaboratorData['email'] ?? '');
        if (! $this->publicContributionSessionIsAuthenticatedForEmail($submittedEmail)) {
            return 'email_unverified';
        }

        return 'ok';
    }

    /**
     * Persist the submitted full name after e-mail verification, when the contribution is stored (item/extra).
     */
    public function applySubmittedFullNameAfterVerifiedContribution(
        Collaborator $collaborator,
        string $submittedFullName,
    ): void {
        $submittedFullName = trim($submittedFullName);
        if ($submittedFullName === '') {
            return;
        }
        if ($this->collaboratorNamesMatch((string) $collaborator->full_name, $submittedFullName)) {
            return;
        }
        $collaborator->forceFill(['full_name' => $submittedFullName])->save();
    }

    public function markPublicContributionSessionAuthenticated(string $email): void
    {
        $email = trim($email);
        session()->put(self::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => $email,
            'expires_at' => now()->addMinutes(EmailVerificationCode::TTL_MINUTES)->getTimestamp(),
        ]);
    }

    public function publicContributionSessionIsAuthenticatedForEmail(string $email): bool
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        $payload = session(self::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY);
        if (! is_array($payload) || ! isset($payload['email'], $payload['expires_at'])) {
            return false;
        }

        $sessionEmail = trim((string) $payload['email']);
        if (! hash_equals(mb_strtolower($sessionEmail), mb_strtolower($email))) {
            return false;
        }

        if (now()->getTimestamp() > (int) $payload['expires_at']) {
            session()->forget(self::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY);

            return false;
        }

        return true;
    }

    public function clearPublicContributionSessionAuth(): void
    {
        session()->forget(self::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY);
        session()->forget(self::PUBLIC_EMAIL_VERIFICATION_PENDING_SESSION_KEY);
    }
}
