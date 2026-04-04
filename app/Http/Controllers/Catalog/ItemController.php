<?php

namespace App\Http\Controllers\Catalog;

use App\Actions\Catalog\StoreItemContributionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Services\Taxonomy\TagCategoryService;
use App\Support\Http\OptionalContentLocale;
use Illuminate\View\View;
use App\Services\Catalog\{ContributionContentLocaleService, ItemCategoryService, ItemImagesService, ItemService};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};

class ItemController extends Controller
{
    public function index(
        Request $request,
        ItemCategoryService $itemCategoryService,
        TagCategoryService $tagCategoryService,
        ItemService $itemService
    ): View|RedirectResponse {
        if ($request->getQueryString() === null || $request->getQueryString() === '') {
            $defaultQuery = http_build_query([
                'item_category' => '',
                'search' => '',
                'order' => '1',
            ]);

            return redirect()->to(route('catalog.items.index', [], false) . '?' . $defaultQuery);
        }

        $data = $itemService->getPaginatedItemsForCatalogIndex($request);
        $itemCategories = $itemCategoryService->getForIndex();
        $categories = $tagCategoryService->getForIndex();

        return view('pages.catalog.items.index', [
            'items' => $data['items'],
            'categoryName' => $data['categoryName'],
            'itemCategories' => $itemCategories,
            'categories' => $categories,
        ]);
    }

    public function create(
        ItemCategoryService $itemCategoryService,
        TagCategoryService $tagCategoryService,
        ContributionContentLocaleService $contributionContentLocaleService
    ): View {
        $categories = $tagCategoryService->getForIndex();
        $itemCategories = $itemCategoryService->getForIndex();
        $localeForm = $contributionContentLocaleService->formOptions();

        return view('pages.catalog.items.create', [
            'categories' => $categories,
            'itemCategories' => $itemCategories,
            'contributionLanguages' => $localeForm['contributionLanguages'],
            'defaultContributionContentLocale' => $localeForm['defaultContentLocale'],
        ]);
    }

    public function store(
        Request $request,
        ItemContributionValidator $itemContributionValidator,
        StoreItemContributionAction $storeItemContributionAction,
        ItemImagesService $itemImagesService,
        ContributionContentLocaleService $contributionContentLocaleService
    ): RedirectResponse {
        $validatedData = $itemContributionValidator->validateStore($request);

        $galleryFiles = $itemImagesService->filterValidGalleryFiles($request->file('gallery_images'));

        $itemPayload = $validatedData['item'];
        $contentLocaleCode = (string) ($itemPayload['content_locale'] ?? '');
        unset($itemPayload['content_locale']);
        $contentLanguageId = $contributionContentLocaleService->languageIdForValidatedCode($contentLocaleCode);

        $result = $storeItemContributionAction->handle(
            $validatedData['collaborator'],
            $itemPayload,
            $contentLanguageId,
            $validatedData['tags'],
            $validatedData['extras'],
            $validatedData['components'],
            $request->file('cover_image'),
            $galleryFiles ?: null
        );

        if ($result['status'] === 'internal_blocked') {
            return back()->withErrors(['contact' => __('app.collaborator.contact_reserved_for_internal')]);
        }

        if ($result['status'] === 'collaborator_blocked') {
            return back()->withErrors(['blocked' => __('app.collaborator.blocked_from_registering')]);
        }

        return redirect()->route('catalog.items.create')->with('success', __('app.catalog.item.contribution_success'));
    }

    public function show(
        string $id,
        ItemCategoryService $itemCategoryService,
        TagCategoryService $tagCategoryService,
        ItemService $itemService,
        ContributionContentLocaleService $contributionContentLocaleService
    ): View {
        $item = $itemService->getPublicItemForShow($id);

        $itemCategories = $itemCategoryService->getForIndex();
        $categories = $tagCategoryService->getForIndex();

        $seriesCategoryId = $item->loadCatalogSeriesTimelineForShow();

        $localeForm = $contributionContentLocaleService->formOptions();

        return view('pages.catalog.items.show', [
            'item' => $item,
            'itemCategories' => $itemCategories,
            'categories' => $categories,
            'seriesCategoryId' => $seriesCategoryId,
            'contributionLanguages' => $localeForm['contributionLanguages'],
            'defaultExtraContentLocale' => $localeForm['defaultContentLocale'],
        ]);
    }

    public function byCategory(Request $request, ItemService $itemService): JsonResponse
    {
        $itemCategoryId = (string) ($request->input('item_category') ?? '');
        $languageId = OptionalContentLocale::languageIdOrNull($request);

        $items = $languageId !== null
            ? $itemService->getPublicItemsByCategoryForLanguage($itemCategoryId, $languageId)
            : $itemService->getPublicItemsByCategory($itemCategoryId);

        return response()->json($items);
    }

    public function componentAutocomplete(Request $request, ItemService $itemService): JsonResponse
    {
        $query = (string) ($request->input('query') ?? '');
        $category = (string) ($request->input('category') ?? '');

        $items = $itemService->getValidatedNamesForComponentAutocomplete($query, $category);

        return response()->json($items);
    }

    public function checkComponentName(Request $request, ItemService $itemService): JsonResponse
    {
        $category = (string) ($request->input('category') ?? '');
        $name = (string) ($request->input('name') ?? '');
        $languageId = OptionalContentLocale::languageIdOrNull($request);

        $count = $itemService->countValidatedByNameAndCategory($name, $category, $languageId);

        return response()->json($count);
    }
}
