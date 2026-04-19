<?php

namespace Tests\Unit\Mail;

use App\Mail\CollaboratorVerificationCodeMail;
use App\Mail\ExtraContributionReceivedMail;
use App\Mail\ItemContributionReceivedMail;
use App\Models\Collaborator\Collaborator;
use Tests\TestCase;

final class CatalogContributionMailablesRenderTest extends TestCase
{
    private string $savedMailFromAddress = '';

    private string $savedMailFromName = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->savedMailFromAddress = (string) config('mail.from.address');
        $this->savedMailFromName = (string) config('mail.from.name');
    }

    protected function tearDown(): void
    {
        config([
            'mail.from.address' => $this->savedMailFromAddress,
            'mail.from.name' => $this->savedMailFromName,
        ]);

        parent::tearDown();
    }

    public function test_item_contribution_received_mail_renders_display_name(): void
    {
        $collaborator = new Collaborator([
            'full_name' => 'Jane Contributor',
            'email' => 'jane@example.com',
        ]);

        $mail = new ItemContributionReceivedMail($collaborator, 'Item X', 'pt_BR');
        $html = $mail->render();

        $this->assertStringContainsString('Jane Contributor', $html);
        $this->assertStringContainsString('Item X', $html);
    }

    public function test_extra_contribution_received_mail_renders_item_and_collaborator(): void
    {
        $collaborator = new Collaborator([
            'full_name' => 'John Extra',
            'email' => 'john.extra@example.com',
        ]);

        $mail = new ExtraContributionReceivedMail($collaborator, 'Item extra Y', 'en');
        $html = $mail->render();

        $this->assertStringContainsString('John Extra', $html);
        $this->assertStringContainsString('Item extra Y', $html);
    }

    public function test_collaborator_verification_code_mail_renders_code_and_greeting_from_form(): void
    {
        config(['mail.from.address' => 'noreply@example.test', 'mail.from.name' => 'E-Museu']);

        $mail = new CollaboratorVerificationCodeMail(null, '654321', 'pt_BR', 'Guest');
        $html = $mail->render();

        $this->assertStringContainsString('654321', $html);
        $this->assertStringContainsString('Guest', $html);
    }

    public function test_collaborator_verification_code_mail_prefers_stored_full_name(): void
    {
        config(['mail.from.address' => 'noreply@example.test', 'mail.from.name' => 'E-Museu']);

        $collaborator = new Collaborator([
            'full_name' => 'Stored DB name',
            'email' => 'u@example.com',
        ]);

        $mail = new CollaboratorVerificationCodeMail($collaborator, '111222', 'en', 'Form name');
        $html = $mail->render();

        $this->assertStringContainsString('111222', $html);
        $this->assertStringContainsString('Stored DB name', $html);
        $this->assertStringNotContainsString('Form name', $html);
    }
}
