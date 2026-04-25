<?php

declare(strict_types=1);

namespace App\Actions\Catalog\StoreItemContribution;

use App\Actions\Catalog\StoreItemContribution\Concerns\{
    CompletesContribution,
    CreatesContributionItem,
    PersistsContribution,
    PreparesContributionItemPayload,
    ResolvesCollaboratorForContribution,
    StoreItemContributionRequestContext,
};
use App\Services\Catalog\{
    ExtraService,
    ItemComponentService,
    ItemImagesService,
    ItemQrCodeService,
    ItemService,
    ItemTagService,
};
use App\Services\Collaborator\CollaboratorService;
use App\Support\Catalog\PublicCatalogContributionOutcome;
use Illuminate\Http\Request;

/**
 * Public catalog item contribution orchestration.
 *
 * {@see self::handle()} validates and persists one HTTP submission. It does **not** set redirect or flash
 * session keys; the HTTP caller (e.g. {@see \App\Http\Controllers\Catalog\ItemController::store}) must
 * attach UI feedback after {@see self::handle()} returns without throwing.
 *
 * {@see \App\Actions\Catalog\StoreItemContribution\Concerns\PersistsContribution::persistContribution()}
 * supports the same payload shape for programmatic callers (e.g. tests).
 */
final class StoreItemContributionAction
{
    use CompletesContribution;
    use CreatesContributionItem;
    use PersistsContribution;
    use PreparesContributionItemPayload;
    use ResolvesCollaboratorForContribution;

    public function __construct(
        private readonly StoreItemContributionRequestContext $requestContext,
        private readonly CollaboratorService $collaboratorService,
        private readonly ExtraService $extraService,
        private readonly ItemComponentService $itemComponentService,
        private readonly ItemTagService $itemTagService,
        private readonly ItemService $itemService,
        private readonly ItemImagesService $itemImagesService,
        private readonly ItemQrCodeService $itemQrCodeService,
    ) {
    }

    /**
     * Run validation, persistence, and post-commit hooks. Does not return an HTTP response; callers must
     * redirect or respond after success, and map validation/domain errors as appropriate.
     */
    public function handle(Request $request): void
    {
        $validatedData = $this->requestContext->validateStore($request);

        $galleryFiles = $this->itemImagesService->filterValidGalleryFiles($request->file('gallery_images'));

        $itemPayload = $validatedData['item'];
        $contentLocaleCode = (string) ($itemPayload['content_locale'] ?? '');
        unset($itemPayload['content_locale']);
        $contentLanguageId = $this->requestContext->languageIdForValidatedLocaleCode($contentLocaleCode);

        $result = $this->persistContribution(
            $validatedData['collaborator'],
            $itemPayload,
            $contentLanguageId,
            $validatedData['tags'],
            $validatedData['extras'],
            $validatedData['components'],
            $request->file('cover_image'),
            $galleryFiles ?: null
        );

        PublicCatalogContributionOutcome::throwUnlessOk($result);

        $this->requestContext->runPostContributionCompletion($result['item'] ?? null, $contentLanguageId);
    }
}
