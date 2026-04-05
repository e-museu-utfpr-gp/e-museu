<?php

namespace App\Actions\Catalog;

use App\Models\Catalog\Extra;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Support\Facades\Log;

/**
 * After a successful public extra contribution: best-effort "received" e-mail, then always clear session auth.
 */
final class CompleteCatalogExtraContributionAction
{
    public function __construct(
        private readonly CollaboratorService $collaborators,
        private readonly SendExtraContributionReceivedMailAction $sendExtraContributionReceivedMail,
    ) {
    }

    public function handle(?Extra $extra, int $contentLanguageId): void
    {
        try {
            if ($extra !== null) {
                $this->sendExtraContributionReceivedMail->handle($extra, $contentLanguageId);
            }
        } catch (\Throwable $e) {
            Log::error('catalog.extra.contribution_received_mail_failed', [
                'extra_id' => $extra->id,
                'exception' => $e,
            ]);
        } finally {
            $this->collaborators->clearPublicContributionSessionAuth();
        }
    }
}
