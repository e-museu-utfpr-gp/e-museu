<?php

namespace App\Actions\Catalog;

use App\Mail\ExtraContributionReceivedMail;
use App\Models\Catalog\Extra;
use App\Models\Collaborator\Collaborator;
use App\Support\Mail\OutgoingMailIsConfigured;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the "extra contribution received" e-mail when mail is configured.
 */
final class SendExtraContributionReceivedMailAction
{
    public function handle(Extra $extra, int $contentLanguageId): void
    {
        if (! OutgoingMailIsConfigured::forDefaultMailer()) {
            return;
        }

        $extra->loadMissing('collaborator', 'item');
        $collaborator = $extra->collaborator;
        $item = $extra->item;
        if (! $collaborator instanceof Collaborator || $item === null) {
            return;
        }

        $translation = $item->translations()->where('language_id', $contentLanguageId)->first();
        $rawName = $translation !== null ? trim((string) ($translation->name ?? '')) : '';
        $itemDisplayName = $rawName !== ''
            ? $rawName
            : (string) __('mail.item_contribution_received.untitled_item');

        Mail::to($collaborator->email)->send(new ExtraContributionReceivedMail(
            $collaborator,
            $itemDisplayName,
            app()->getLocale(),
        ));
    }
}
