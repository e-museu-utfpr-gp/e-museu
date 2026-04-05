<?php

namespace App\Actions\Catalog;

use App\Models\Catalog\Item;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Support\Facades\Log;

/**
 * After a successful public item contribution: best-effort "received" e-mail, then always clear session auth
 * so a mail transport failure still returns success to the user and the verification session is not left half-open.
 */
final class CompleteCatalogItemContributionAction
{
    public function __construct(
        private readonly CollaboratorService $collaborators,
        private readonly SendItemContributionReceivedMailAction $sendContributionReceivedMail,
    ) {
    }

    public function handle(?Item $item, int $contentLanguageId): void
    {
        try {
            if ($item !== null) {
                $this->sendContributionReceivedMail->handle($item, $contentLanguageId);
            }
        } catch (\Throwable $e) {
            Log::error('catalog.item.contribution_received_mail_failed', [
                'item_id' => $item->id,
                'exception' => $e,
            ]);
        } finally {
            $this->collaborators->clearPublicContributionSessionAuth();
        }
    }
}
