<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Collaborator\Collaborator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollaboratorVerificationCodeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  string|null  $greetingDisplayName  Fallback when there is no stored collaborator (or DB name is empty),
     *                                             e.g. form input for a first-time address.
     */
    public function __construct(
        public ?Collaborator $collaborator,
        public string $code,
        string $siteUiLocale,
        public ?string $greetingDisplayName = null,
    ) {
        $this->locale = $siteUiLocale;
    }

    public function envelope(): Envelope
    {
        $fromAddress = (string) config('mail.from.address');
        $fromName = trim((string) config('mail.from.name'));

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: __('mail.collaborator_verification_code.subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        $fromDb = trim((string) ($this->collaborator?->full_name ?? ''));
        $fromForm = trim((string) ($this->greetingDisplayName ?? ''));
        $displayName = $fromDb !== '' ? $fromDb : $fromForm;

        return new Content(
            view: 'emails.collaborator-verification-code',
            with: [
                'collaboratorName' => $displayName,
                'code' => $this->code,
            ],
        );
    }
}
