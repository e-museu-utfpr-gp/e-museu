<?php

namespace App\Http\Controllers\Catalog;

use App\Actions\Catalog\StoreItemContributionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Catalog\ItemImagesService;
use App\Services\Catalog\ItemService;
use App\Services\Taxonomy\TagCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(
        Request $request,
        ItemCategoryService $itemCategoryService,
        TagCategoryService $tagCategoryService,
        ItemService $itemService
    ): View {
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
        TagCategoryService $tagCategoryService
    ): View {
        $categories = $tagCategoryService->getForIndex();
        $itemCategories = $itemCategoryService->getForIndex();

        return view('pages.catalog.items.create', compact('categories', 'itemCategories'));
    }

    public function store(
        Request $request,
        ItemContributionValidator $itemContributionValidator,
        StoreItemContributionAction $storeItemContributionAction,
        ItemImagesService $itemImagesService
    ): RedirectResponse {
        $validatedData = $itemContributionValidator->validateStore($request);

        $galleryFiles = $itemImagesService->filterValidGalleryFiles($request->file('gallery_images'));

        $result = $storeItemContributionAction->handle(
            $validatedData['collaborator'],
            $validatedData['item'],
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
        ItemService $itemService
    ): View {
        $item = $itemService->getPublicItemForShow($id);

        $itemCategories = $itemCategoryService->getForIndex();
        $categories = $tagCategoryService->getForIndex();

        $seriesCategoryId = $categories
            ->first(static fn ($c): bool => in_array($c->name, ['Série', 'Series'], true))
            ?->id;

        return view('pages.catalog.items.show', compact('item', 'itemCategories', 'categories', 'seriesCategoryId'));
    }

    public function edit(): never
    {
        abort(404);
    }

    public function byCategory(Request $request, ItemService $itemService): JsonResponse
    {
        $itemCategoryId = (string) ($request->input('item_category') ?? '');

        $items = $itemService->getPublicItemsByCategory($itemCategoryId);

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

        $count = $itemService->countValidatedByNameAndCategory($name, $category);

        return response()->json($count);
    }
}
