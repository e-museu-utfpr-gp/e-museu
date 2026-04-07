<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\Language;
use App\Services\Collaborator\CollaboratorService;
use App\Services\Identity\LockService;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use App\Http\Requests\Admin\Catalog\{AdminStoreItemRequest, AdminUpdateItemRequest};
use App\Models\Catalog\{Item, ItemImage};
use App\Services\Catalog\{ItemCategoryService, ItemImagesService, ItemQrCodeService, ItemService};
use App\Services\LocationService;
use App\Support\Admin\{AdminEditHeadingLocale, AdminIndexTableView};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Throwable;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdminItemController extends AdminBaseController
{
    public function index(Request $request, ItemService $itemService, LocationService $locationService): View
    {
        $result = $itemService->getPaginatedItemsForAdminIndex($request);

        $searchSelectOptions = $locationService->orderedForForms()->map(static fn ($loc): array => [
            'value' => (string) $loc->id,
            'label' => $loc->localized_label,
        ])->all();

        return view('pages.admin.catalog.items.index', array_merge([
            'items' => $result['items'],
            'count' => $result['count'],
            'searchSelectColumns' => ['location_id'],
            'searchSelectOptions' => $searchSelectOptions,
            'searchSelectAnyLabel' => __('view.admin.catalog.items.index.search_location_any'),
        ], AdminIndexTableView::catalogItems()));
    }

    public function byItemCategory(Request $request, ItemService $itemService): JsonResponse
    {
        $itemCategoryId = (string) ($request->input('item_category') ?? '');
        $items = $itemService->getItemsByItemCategoryForAdminSelect($itemCategoryId);

        return response()->json($itemService->mapItemsToCategorySelectJson($items));
    }

    public function show(Item $item, ItemQrCodeService $itemQrCodeService): View
    {
        $item->load(Item::eagerLoadRelationsForAdminShow());
        $qrCodeImage = $itemQrCodeService->qrCodeImageForItem($item);
        $qrCodeTargetUrl = $itemQrCodeService->targetUrlFromQrImage($qrCodeImage)
            ?? $itemQrCodeService->destinationUrlForItem($item);
        $qrDomainInvalid = ! $itemQrCodeService->isQrDomainCompatible($qrCodeTargetUrl);

        return view('pages.admin.catalog.items.show', compact(
            'item',
            'qrCodeImage',
            'qrDomainInvalid',
            'qrCodeTargetUrl'
        ));
    }

    public function create(
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService,
        LocationService $locationService,
    ): View {
        $locationForm = $locationService->forItemCreateForms();

        return view('pages.admin.catalog.items.create', [
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
            'contentLanguages' => Language::forCatalogContentForms(),
            'preferredContentTabLanguageId' => AdminEditHeadingLocale::preferredContentTabLanguageId(),
            'locations' => $locationForm['locations'],
            'defaultCatalogLocationId' => $locationForm['defaultCatalogLocationId'],
        ]);
    }

    public function store(
        AdminStoreItemRequest $request,
        ItemService $itemService,
        ItemImagesService $itemImagesService,
        ItemQrCodeService $itemQrCodeService
    ): RedirectResponse {
        $item = $itemService->createItemWithIdentificationCode($request);
        $itemImagesService->storeImagesFromStoreRequest($item, $request);
        try {
            $itemQrCodeService->regenerateForItem($item);
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('admin.catalog.items.show', $item->id)
            ->with('success', __('app.catalog.item.created'));
    }

    public function edit(
        Item $item,
        ItemCategoryService $itemCategoryService,
        CollaboratorService $collaboratorService,
        LocationService $locationService,
        LockService $lockService,
        AdminEditHeadingLocale $headingLocale,
        ItemQrCodeService $itemQrCodeService
    ): View {
        $item->load(['images', 'translations.language']);
        $lockService->requireUnlockedThenLock($item);
        $qrCodeImage = $itemQrCodeService->qrCodeImageForItem($item);
        $qrCodeTargetUrl = $itemQrCodeService->targetUrlFromQrImage($qrCodeImage)
            ?? $itemQrCodeService->destinationUrlForItem($item);
        $qrDomainInvalid = ! $itemQrCodeService->isQrDomainCompatible($qrCodeTargetUrl);

        return view('pages.admin.catalog.items.edit', array_merge([
            'item' => $item,
            'contentLanguages' => Language::forCatalogContentForms(),
            'itemCategories' => $itemCategoryService->getForForm(),
            'collaborators' => $collaboratorService->getForForm(),
            'locations' => $locationService->orderedForForms(),
            'qrCodeImage' => $qrCodeImage,
            'qrDomainInvalid' => $qrDomainInvalid,
            'qrCodeTargetUrl' => $qrCodeTargetUrl,
        ], $headingLocale->resolveFor($item)));
    }

    public function update(
        AdminUpdateItemRequest $request,
        Item $item,
        ItemImagesService $itemImagesService,
        ItemService $itemService,
        ItemQrCodeService $itemQrCodeService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $originalIdentificationCode = (string) $item->identification_code;

        $data = Arr::except($request->validated(), [
            'image',
            'gallery_images',
            'delete_image_ids',
            'set_cover_image_id',
        ]);
        // Same as contribution: validated() skips absent keys; cleared `<input type="date">`
        // is often omitted from POST, so we must persist null explicitly.
        if (! array_key_exists('date', $data)) {
            $data['date'] = $request->input('date');
        }

        $itemImagesService->processDeleteImageIds($item, $request);
        $itemImagesService->processCoverImage($item, $request);
        $itemImagesService->processGalleryImages($item, $request);

        $itemService->updateItem($item, $data);
        $item->refresh();

        if ((string) $item->identification_code !== $originalIdentificationCode) {
            try {
                $itemQrCodeService->regenerateForItem($item);
            } catch (Throwable $e) {
                report($e);
            }
        }

        $lockService->unlock($item);

        return redirect()->route('admin.catalog.items.show', $item)->with('success', __('app.catalog.item.updated'));
    }

    public function regenerateQrCode(
        Item $item,
        ItemQrCodeService $itemQrCodeService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $itemQrCodeService->regenerateForItem($item);

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item.qrcode_regenerated'));
    }

    public function deleteQrCode(
        Item $item,
        ItemQrCodeService $itemQrCodeService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $itemQrCodeService->deleteForItem($item);

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item.qrcode_deleted'));
    }

    public function destroy(
        Item $item,
        ItemImagesService $itemImagesService,
        ItemService $itemService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);

        $lockService->unlock($item);

        $itemImagesService->deleteAllImagesForItem($item);
        $itemService->deleteItem($item);

        return redirect()->route('admin.catalog.items.index')->with('success', __('app.catalog.item.deleted'));
    }

    public function destroyImage(
        Item $item,
        ItemImage $image,
        ItemImagesService $itemImagesService,
        LockService $lockService
    ): RedirectResponse {
        $lockService->requireUnlocked($item);
        $itemImagesService->deleteImage($item, $image);

        return redirect()
            ->route('admin.catalog.items.edit', $item)
            ->with('success', __('app.catalog.item_image.deleted'));
    }
}
