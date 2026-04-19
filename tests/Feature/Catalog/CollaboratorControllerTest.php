<?php

namespace Tests\Feature\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Http\Middleware\VerifyAntiBotChallenge;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class CollaboratorControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyAntiBotChallenge::class);
    }

    public function test_clear_contribution_session_returns_ok(): void
    {
        $this->postJson(route('catalog.collaborators.clear-contribution-session'))
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }

    public function test_clear_contribution_session_forgets_public_auth_session(): void
    {
        $email = 'clear-session-' . uniqid('', false) . '@google.com';

        $this->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ]);

        $this->postJson(route('catalog.collaborators.clear-contribution-session'))
            ->assertOk();

        $this->postJson(route('catalog.collaborators.check-contact'), ['email' => $email])
            ->assertOk()
            ->assertJson(['contribution_session_verified' => false]);
    }

    public function test_check_contact_unknown_email_returns_not_exists_without_skip_contact_check(): void
    {
        $email = 'unknown-ctrl-' . uniqid('', false) . '@google.com';

        $response = $this->postJson(route('catalog.collaborators.check-contact'), ['email' => $email])
            ->assertOk();

        $response->assertJson([
            'exists' => false,
            'internal_reserved' => false,
            'full_name' => '',
            'email_verified' => true,
            'contribution_session_verified' => false,
            'name_differs_from_record' => false,
            'collaborator_id' => null,
        ]);

        $this->assertArrayNotHasKey('skip_contact_check', $response->json());
    }

    public function test_check_contact_external_without_db_verification_shows_email_unverified(): void
    {
        $email = 'ext-unver-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'No Code Yet',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => null,
        ]);

        $this->postJson(route('catalog.collaborators.check-contact'), ['email' => $email])
            ->assertOk()
            ->assertJson([
                'exists' => true,
                'email_verified' => false,
                'internal_reserved' => false,
            ]);
    }

    public function test_check_contact_external_with_db_verification_shows_email_verified(): void
    {
        $email = 'ext-ver-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Verified',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $this->postJson(route('catalog.collaborators.check-contact'), ['email' => $email])
            ->assertOk()
            ->assertJson([
                'exists' => true,
                'email_verified' => true,
            ]);
    }

    public function test_check_contact_reports_contribution_session_verified_when_session_matches(): void
    {
        $email = 'sess-match-' . uniqid('', false) . '@google.com';

        $this->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->postJson(route('catalog.collaborators.check-contact'), ['email' => $email])
            ->assertOk()
            ->assertJson(['contribution_session_verified' => true]);
    }

    public function test_check_contact_omitted_email_behaves_like_empty_payload(): void
    {
        $this->postJson(route('catalog.collaborators.check-contact'), [])
            ->assertOk()
            ->assertJson([
                'exists' => false,
                'skip_contact_check' => true,
                'email_verified' => null,
            ]);
    }

    public function test_request_verification_code_returns_validation_errors_when_fields_missing(): void
    {
        Mail::fake();

        $this->postJson(route('catalog.collaborators.request-verification-code'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'full_name']);
    }

    public function test_confirm_verification_code_returns_validation_error_when_code_not_six_digits(): void
    {
        $this->postJson(route('catalog.collaborators.confirm-verification-code'), [
            'email' => 'x@google.com',
            'code' => '12ab45',
            'full_name' => 'Name',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code']);
    }

    public function test_request_verification_code_returns_403_when_external_collaborator_blocked(): void
    {
        Mail::fake();

        $email = 'blocked-ctrl-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Blocked',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => true,
        ]);

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Anyone',
        ])
            ->assertForbidden()
            ->assertJsonFragment(['message' => __('app.collaborator.verify_blocked')]);

        Mail::assertNothingSent();
    }
}
