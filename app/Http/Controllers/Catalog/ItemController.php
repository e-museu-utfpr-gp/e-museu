<?php

declare(strict_types=1);

namespace App\Http\Controllers\Catalog;

use App\Actions\Catalog\StoreItemContribution\StoreItemContributionAction;
use App\Http\Controllers\Controller;
use App\Services\Catalog\{CatalogItemViewDataService, ItemService};
use App\Support\Http\OptionalContentLocale;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request, CatalogItemViewDataService $viewData): View|RedirectResponse
    {
        if ($request->getQueryString() === null || $request->getQueryString() === '') {
            $defaultQuery = http_build_query([
                'item_category' => '',
                'search' => '',
                'order' => '1',
            ]);

            return redirect()->to(route('catalog.items.index', [], false) . '?' . $defaultQuery);
        }

        return view('pages.catalog.items.index', $viewData->forIndex($request));
    }

    public function create(CatalogItemViewDataService $viewData): View
    {
        return view('pages.catalog.items.create', $viewData->forCreate());
    }

    public function store(Request $request, StoreItemContributionAction $storeItemContribution): RedirectResponse
    {
        $storeItemContribution->handle($request);

        return redirect()->route('catalog.items.create')->with('success', __('app.catalog.item.contribution_success'));
    }

    public function show(string $id, CatalogItemViewDataService $viewData): View
    {
        return view('pages.catalog.items.show', $viewData->forShow($id));
    }

    public function byCategory(Request $request, ItemService $itemService): JsonResponse
    {
        $itemCategoryId = (string) ($request->input('item_category') ?? '');
        $languageId = OptionalContentLocale::languageIdOrNull($request);

        $items = $languageId !== null
            ? $itemService->getPublicItemsByCategoryForLanguage($itemCategoryId, $languageId)
            : $itemService->getPublicItemsByCategory($itemCategoryId);

        return response()->json($itemService->mapItemsToCategorySelectJson($items));
    }

    public function componentAutocomplete(Request $request, ItemService $itemService): JsonResponse
    {
        $query = (string) ($request->input('query') ?? '');
        $category = (string) ($request->input('category') ?? '');

        $items = $itemService->getValidatedNamesForComponentAutocomplete($query, $category);

        return response()->json($itemService->mapItemsForComponentAutocompleteJson($items));
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
