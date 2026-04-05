<?php

namespace App\Actions\Catalog;

use App\Mail\ItemContributionReceivedMail;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Support\Mail\OutgoingMailIsConfigured;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the "contribution received" e-mail to the collaborator when mail is configured.
 */
final class SendItemContributionReceivedMailAction
{
    public function handle(Item $item, int $contentLanguageId): void
    {
        if (! OutgoingMailIsConfigured::forDefaultMailer()) {
            return;
        }

        $item->loadMissing('collaborator');
        $collaborator = $item->collaborator;
        if (! $collaborator instanceof Collaborator) {
            return;
        }

        $translation = $item->translations()->where('language_id', $contentLanguageId)->first();
        $rawName = $translation !== null ? trim((string) ($translation->name ?? '')) : '';
        $itemDisplayName = $rawName !== ''
            ? $rawName
            : (string) __('mail.item_contribution_received.untitled_item');

        Mail::to($collaborator->email)->send(new ItemContributionReceivedMail(
            $collaborator,
            $itemDisplayName,
            app()->getLocale(),
        ));
    }
}
