<?php

declare(strict_types=1);

namespace App\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Language;
use App\Services\Collaborator\CollaboratorService;
use App\Services\LocationService;
use App\Support\Admin\{AdminEditHeadingLocale, AdminIndexTableView};
use Illuminate\Http\Request;

/**
 * View payloads for admin item index/create/edit/show.
 *
 * Keeps {@see \App\Http\Controllers\Admin\Catalog\Item\AdminItemController} coupling low for PHPMD.
 */
final class AdminItemAdminViewDataService
{
    public function __construct(
        private readonly ItemCategoryService $itemCategoryService,
        private readonly CollaboratorService $collaboratorService,
        private readonly LocationService $locationService,
        private readonly ItemQrCodeService $itemQrCodeService,
        private readonly AdminEditHeadingLocale $headingLocale,
        private readonly ItemService $itemService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function forIndex(Request $request): array
    {
        $result = $this->itemService->getPaginatedItemsForAdminIndex($request);

        $searchSelectOptions = $this->locationService->orderedForForms()->map(static fn ($loc): array => [
            'value' => (string) $loc->id,
            'label' => $loc->localized_label,
        ])->all();

        return array_merge([
            'items' => $result['items'],
            'count' => $result['count'],
            'searchSelectColumns' => ['location_id'],
            'searchSelectOptions' => $searchSelectOptions,
            'searchSelectAnyLabel' => __('view.admin.catalog.items.index.search_location_any'),
        ], AdminIndexTableView::catalogItems());
    }

    /**
     * @return array<string, mixed>
     */
    public function forCreate(): array
    {
        $locationForm = $this->locationService->forItemCreateForms();

        return [
            'itemCategories' => $this->itemCategoryService->getForForm(),
            'collaborators' => $this->collaboratorService->getForForm(),
            'contentLanguages' => Language::forCatalogContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
            'locations' => $locationForm['locations'],
            'defaultCatalogLocationId' => $locationForm['defaultCatalogLocationId'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forEdit(Item $item): array
    {
        $item->load(['images', 'translations.language']);

        return array_merge([
            'item' => $item,
            'contentLanguages' => Language::forCatalogContentForms(),
            'itemCategories' => $this->itemCategoryService->getForForm(),
            'collaborators' => $this->collaboratorService->getForForm(),
            'locations' => $this->locationService->orderedForForms(),
        ], $this->itemQrCodeService->adminViewQrPresentation($item), $this->headingLocale->resolveFor($item));
    }

    /**
     * @return array<string, mixed>
     */
    public function forShow(Item $item): array
    {
        $item->load(Item::eagerLoadRelationsForAdminShow());

        return array_merge(
            ['item' => $item],
            $this->itemQrCodeService->adminViewQrPresentation($item),
        );
    }
}
