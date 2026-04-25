<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Collaborator\Collaborator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItemContributionReceivedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Collaborator $collaborator,
        public string $itemDisplayName,
        string $siteUiLocale,
    ) {
        $this->locale = $siteUiLocale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.item_contribution_received.subject', [
                'app' => config('app.name'),
                'item' => $this->itemDisplayName,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.item-contribution-received',
            with: [
                'collaboratorName' => $this->collaborator->full_name,
                'itemDisplayName' => $this->itemDisplayName,
            ],
        );
    }
}
