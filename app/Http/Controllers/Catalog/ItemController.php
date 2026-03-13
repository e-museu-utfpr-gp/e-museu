<?php

namespace App\Http\Controllers\Catalog;

use App\Actions\Catalog\StoreItemContributionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Services\Catalog\ItemCategoryService;
use App\Services\Catalog\ItemImagesService;
use App\Services\Catalog\ItemIndexQueryBuilder;
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
        ItemIndexQueryBuilder $itemIndexQueryBuilder,
        TagCategoryService $tagCategoryService
    ): View {
        $indexResult = $itemIndexQueryBuilder->build($request);
        $itemCategories = $itemCategoryService->getForIndex();
        $categories = $tagCategoryService->getForIndex();

        return view('catalog.items.index', [
            'items' => $indexResult['items'],
            'categoryName' => $indexResult['categoryName'],
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

        return view('catalog.items.create', compact('categories', 'itemCategories'));
    }

    public function store(
        Request $request,
        ItemContributionValidator $itemContributionValidator,
        StoreItemContributionAction $storeItemContributionAction,
        ItemImagesService $itemImagesService
    ): RedirectResponse {
        $validatedData = $itemContributionValidator->validateStore($request);

        $galleryFiles = $itemImagesService->filterValidGalleryFiles($request->file('gallery_images'));

        return $storeItemContributionAction->handle(
            $validatedData['collaborator'],
            $validatedData['item'],
            $validatedData['tags'],
            $validatedData['extras'],
            $validatedData['components'],
            $request->file('cover_image'),
            $galleryFiles ?: null
        );
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

        return view('catalog.items.show', compact('item', 'itemCategories', 'categories'));
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
}
