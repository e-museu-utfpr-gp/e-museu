<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Support\Facades\Log;

/**
 * After a successful public catalog contribution: best-effort "received" e-mail, then always clear session auth
 * so mail transport failure still returns success to the user and verification session is not left half-open.
 */
final class CatalogContributionCompletionService
{
    public function __construct(
        private readonly CollaboratorService $collaborators,
        private readonly CatalogContributionReceivedMailService $contributionReceivedMail,
    ) {
    }

    public function afterItem(?Item $item, int $contentLanguageId): void
    {
        $this->finalize(
            function () use ($item, $contentLanguageId): void {
                if ($item !== null) {
                    $this->contributionReceivedMail->sendForItem($item, $contentLanguageId);
                }
            },
            'catalog.item.contribution_received_mail_failed',
            'item_id',
            $item?->id,
        );
    }

    public function afterExtra(?Extra $extra, int $contentLanguageId): void
    {
        $this->finalize(
            function () use ($extra, $contentLanguageId): void {
                if ($extra !== null) {
                    $this->contributionReceivedMail->sendForExtra($extra, $contentLanguageId);
                }
            },
            'catalog.extra.contribution_received_mail_failed',
            'extra_id',
            $extra?->id,
        );
    }

    private function finalize(\Closure $trySendMail, string $logKey, string $idKey, ?int $entityId): void
    {
        try {
            $trySendMail();
        } catch (\Throwable $e) {
            Log::error($logKey, [
                $idKey => $entityId,
                'exception' => $e,
            ]);
        } finally {
            $this->collaborators->clearPublicContributionSessionAuth();
        }
    }
}
