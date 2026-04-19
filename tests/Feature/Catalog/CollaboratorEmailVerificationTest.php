<?php

namespace Tests\Feature\Catalog;

use App\Enums\Collaborator\CollaboratorRole;
use App\Http\Middleware\VerifyAntiBotChallenge;
use App\Mail\CollaboratorVerificationCodeMail;
use Illuminate\Mail\Mailables\Address;
use App\Models\Collaborator\Collaborator;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class CollaboratorEmailVerificationTest extends AbstractMysqlRefreshDatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mail/session behaviour only, not Turnstile; stable if CI sets ANTIBOT_DRIVER=turnstile.
        $this->withoutMiddleware(VerifyAntiBotChallenge::class);
    }

    public function test_request_and_confirm_code_verifies_external_collaborator(): void
    {
        Mail::fake();

        $email = 'verify-code-' . uniqid('', false) . '@google.com';

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Test User',
        ])->assertOk()->assertJsonFragment([
            'message' => __('app.collaborator.verify_code_sent'),
        ]);

        $this->assertDatabaseMissing('collaborators', ['email' => $email]);

        $sentMail = null;
        Mail::assertSent(
            CollaboratorVerificationCodeMail::class,
            function (CollaboratorVerificationCodeMail $mail) use (&$sentMail): bool {
                $sentMail = $mail;

                return strlen($mail->code) === 6;
            }
        );
        $this->assertInstanceOf(CollaboratorVerificationCodeMail::class, $sentMail);
        $code = $sentMail->code;

        $confirm = $this->postJson(route('catalog.collaborators.confirm-verification-code'), [
            'email' => $email,
            'code' => $code,
            'full_name' => 'Test User',
        ])->assertOk();

        $confirm->assertJsonFragment([
            'message' => __('app.collaborator.verify_confirmed'),
        ]);

        $collaborator = Collaborator::query()->where('email', $email)->first();
        $this->assertNotNull($collaborator);
        $this->assertTrue($collaborator->hasVerifiedEmail());
        $confirm->assertJsonFragment(['collaborator_id' => $collaborator->id]);
    }

    public function test_request_verification_code_does_not_insert_collaborator_row(): void
    {
        Mail::fake();

        $email = 'no-row-yet-' . uniqid('', false) . '@google.com';

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Pending Only',
        ])->assertOk();

        $this->assertDatabaseMissing('collaborators', ['email' => $email]);
    }

    public function test_confirm_without_pending_session_returns_422(): void
    {
        $email = 'no-session-pending-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Existing',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
        ]);

        $this->postJson(route('catalog.collaborators.confirm-verification-code'), [
            'email' => $email,
            'code' => '123456',
            'full_name' => 'Existing',
        ])->assertStatus(422)->assertJsonFragment(['message' => __('app.collaborator.verify_invalid_code')]);
    }

    public function test_wrong_code_returns_422(): void
    {
        Mail::fake();

        $email = 'verify-wrong-' . uniqid('', false) . '@google.com';

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Wrong Code User',
        ])->assertOk();

        $this->postJson(route('catalog.collaborators.confirm-verification-code'), [
            'email' => $email,
            'code' => '000000',
            'full_name' => 'Anyone',
        ])->assertStatus(422)->assertJsonFragment(['message' => __('app.collaborator.verify_invalid_code')]);
    }

    public function test_internal_email_cannot_request_code(): void
    {
        Mail::fake();

        $email = 'internal-block-' . uniqid('', false) . '@google.com';

        Collaborator::create([
            'full_name' => 'Internal',
            'email' => $email,
            'role' => CollaboratorRole::INTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Someone',
        ])->assertStatus(422)->assertJsonFragment(['message' => __('app.collaborator.verify_internal_reserved')]);

        Mail::assertNothingSent();
    }

    public function test_verified_collaborator_can_request_a_new_code(): void
    {
        Mail::fake();

        $email = 'reverify-' . uniqid('', false) . '@google.com';

        Collaborator::create([
            'full_name' => 'Original Name',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'New Name',
        ])->assertOk()->assertJsonFragment([
            'message' => __('app.collaborator.verify_code_sent'),
        ]);

        Mail::assertSent(
            CollaboratorVerificationCodeMail::class,
            function (CollaboratorVerificationCodeMail $mail): bool {
                if ($mail->greetingDisplayName !== 'New Name') {
                    return false;
                }
                if ($mail->collaborator === null || $mail->collaborator->full_name !== 'Original Name') {
                    return false;
                }
                $html = $mail->render();

                return str_contains($html, 'Original Name') && ! str_contains($html, 'New Name');
            },
        );

        $collaborator = Collaborator::query()->where('email', $email)->first();
        $this->assertNotNull($collaborator);
        $this->assertSame('Original Name', $collaborator->full_name);
    }

    public function test_verification_email_uses_site_ui_locale_from_session(): void
    {
        Mail::fake();

        $email = 'verify-locale-' . uniqid('', false) . '@google.com';

        $this->withSession(['locale' => 'pt_BR'])
            ->postJson(route('catalog.collaborators.request-verification-code'), [
                'email' => $email,
                'full_name' => 'Locale User',
            ])
            ->assertOk();

        Mail::assertSent(
            CollaboratorVerificationCodeMail::class,
            function (CollaboratorVerificationCodeMail $mail): bool {
                return $mail->locale === 'pt_BR';
            }
        );
    }

    public function test_check_contact_marks_internal_email(): void
    {
        $email = 'check-internal-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Inst',
            'email' => $email,
            'role' => CollaboratorRole::INTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $response = $this->postJson(route('catalog.collaborators.check-contact'), [
            'email' => $email,
        ]);

        $response->assertOk()->assertJson([
            'internal_reserved' => true,
            'exists' => false,
            'name_differs_from_record' => false,
            'skip_contact_check' => true,
            'email_verified' => null,
        ]);

        $payload = $response->json();
        $this->assertIsArray($payload);
        $this->assertArrayNotHasKey('full_name', $payload);
    }

    public function test_check_contact_empty_email_skips_lookup_semantics(): void
    {
        $this->postJson(route('catalog.collaborators.check-contact'), ['email' => ''])
            ->assertOk()
            ->assertJson([
                'exists' => false,
                'skip_contact_check' => true,
                'email_verified' => null,
            ]);
    }

    public function test_check_contact_rejects_invalid_email_format(): void
    {
        $this->postJson(route('catalog.collaborators.check-contact'), ['email' => 'not-an-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_check_contact_external_not_internal_reserved(): void
    {
        $email = 'check-ext-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Ext',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
        ]);

        $this->postJson(route('catalog.collaborators.check-contact'), ['email' => $email])
            ->assertOk()
            ->assertJson([
                'internal_reserved' => false,
                'exists' => true,
                'name_differs_from_record' => false,
            ]);
    }

    public function test_check_contact_reports_when_submitted_name_differs_from_record(): void
    {
        $email = 'check-name-diff-' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'On File',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
        ]);

        $this->postJson(route('catalog.collaborators.check-contact'), [
            'email' => $email,
            'full_name' => 'Different Typing',
        ])
            ->assertOk()
            ->assertJson([
                'exists' => true,
                'name_differs_from_record' => true,
                'full_name' => 'On File',
            ]);
    }

    public function test_verification_mailable_uses_config_from_name_only(): void
    {
        Mail::fake();
        config(['mail.from.name' => 'Museum Site', 'mail.from.address' => 'noreply@example.org']);

        $email = 'from-name-' . uniqid('', false) . '@google.com';

        $this->postJson(route('catalog.collaborators.request-verification-code'), [
            'email' => $email,
            'full_name' => 'Attacker Display Name',
        ])->assertOk();

        Mail::assertSent(
            CollaboratorVerificationCodeMail::class,
            function (CollaboratorVerificationCodeMail $mail): bool {
                $envelope = $mail->envelope();
                $from = $envelope->from;
                if (! $from instanceof Address) {
                    return false;
                }

                return $from->address === 'noreply@example.org'
                    && $from->name === 'Museum Site';
            },
        );
    }
}
