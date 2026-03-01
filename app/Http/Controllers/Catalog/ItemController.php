<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ItemContributionValidator;
use App\Http\Requests\Catalog\SingleExtraRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Taxonomy\TagCategory;
use App\Services\Catalog\ItemContributionService;
use App\Services\Catalog\ItemIndexQueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private ItemIndexQueryBuilder $itemIndexQueryBuilder,
        private ItemContributionService $itemContributionService,
        private ItemContributionValidator $itemContributionValidator
    ) {
    }

    public function index(Request $request): View
    {
        $indexResult = $this->itemIndexQueryBuilder->build($request);
        $sections = $this->itemContributionService->loadSections();
        $categories = $this->itemContributionService->loadCategories();

        return view('catalog.items.index', [
            'items' => $indexResult['items'],
            'sectionName' => $indexResult['sectionName'],
            'sections' => $sections,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $categories = TagCategory::all();
        $sections = ItemCategory::all();

        return view('catalog.items.create', compact('categories', 'sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->itemContributionValidator->validateStore($request);

        return $this->itemContributionService->store(
            $validatedData['collaborator'],
            $validatedData['item'],
            $validatedData['tags'],
            $validatedData['extras'],
            $validatedData['components'],
            $request->file('image')
        );
    }

    public function show(string $id): View
    {
        $item = Item::findOrFail($id);
        $sections = ItemCategory::get();
        $categories = TagCategory::get();

        return view('catalog.items.show', compact('item', 'sections', 'categories'));
    }

    public function edit(Item $item): never
    {
        abort(404);
    }

    public function storeSingleExtra(SingleExtraRequest $request): RedirectResponse
    {
        $validatedData = $this->itemContributionValidator->validateSingleExtra($request);

        return $this->itemContributionService->storeSingleExtra(
            $validatedData['collaborator'],
            $validatedData['extra']
        );
    }

    public function bySection(Request $request): JsonResponse
    {
        $section = (string) ($request->input('section') ?? '');

        $data = Item::where('category_id', 'LIKE', $section)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($data);
    }
}
