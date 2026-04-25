<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Mail\ExtraContributionReceivedMail;
use App\Mail\ItemContributionReceivedMail;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Support\Mail\OutgoingMailIsConfigured;
use Illuminate\Support\Facades\Mail;

/**
 * Sends "contribution received" mail for public item or extra flows when outbound mail is configured.
 *
 * Product note: `POST catalog/items` and `POST catalog/extras` do not use the Turnstile middleware (unlike
 * requesting a verification e-mail). Automated submissions are constrained mainly by route throttling and
 * contribution rules; see also {@see \App\Http\Controllers\Catalog\ItemController::store} and extras flow.
 */
final class CatalogContributionReceivedMailService
{
    public function sendForItem(Item $item, int $contentLanguageId): void
    {
        if (! OutgoingMailIsConfigured::forDefaultMailer()) {
            return;
        }

        $item->loadMissing('collaborator');
        $collaborator = $item->collaborator;
        if (! $collaborator instanceof Collaborator) {
            return;
        }

        $itemDisplayName = $this->itemDisplayNameForContentLanguage($item, $contentLanguageId);

        Mail::to($collaborator->email)->send(new ItemContributionReceivedMail(
            $collaborator,
            $itemDisplayName,
            app()->getLocale(),
        ));
    }

    public function sendForExtra(Extra $extra, int $contentLanguageId): void
    {
        if (! OutgoingMailIsConfigured::forDefaultMailer()) {
            return;
        }

        $extra->loadMissing('collaborator', 'item');
        $collaborator = $extra->collaborator;
        $item = $extra->item;
        if (! $collaborator instanceof Collaborator || ! $item instanceof Item) {
            return;
        }

        $displayName = $this->itemDisplayNameForContentLanguage($item, $contentLanguageId);

        Mail::to($collaborator->email)->send(new ExtraContributionReceivedMail(
            $collaborator,
            $displayName,
            app()->getLocale(),
        ));
    }

    private function itemDisplayNameForContentLanguage(Item $item, int $contentLanguageId): string
    {
        $translation = $item->translations()->where('language_id', $contentLanguageId)->first();
        $rawName = $translation !== null ? trim((string) ($translation->name ?? '')) : '';

        return $rawName !== ''
            ? $rawName
            : (string) __('mail.item_contribution_received.untitled_item');
    }
}
