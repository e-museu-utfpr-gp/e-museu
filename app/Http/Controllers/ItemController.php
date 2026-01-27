<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemContributionValidator;
use App\Http\Requests\SingleExtraRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Section;
use App\Services\ItemContributionService;
use App\Services\ItemIndexQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

        return view('items/index', [
            'items' => $indexResult['items'],
            'sectionName' => $indexResult['sectionName'],
            'sections' => $sections,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $categories = Category::all();
        $sections = Section::all();

        return view('items/create', compact('categories', 'sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->itemContributionValidator->validateStore($request);

        return $this->itemContributionService->store(
            $validatedData['proprietary'],
            $validatedData['item'],
            $validatedData['tags'],
            $validatedData['extras'],
            $validatedData['components'],
            $request->file('image')
        );
    }

    public function show(string $id): View
    {
        $item = Item::find($id);
        $sections = Section::get();
        $categories = Category::get();

        return view('items.show', compact('item', 'sections', 'categories'));
    }

    public function edit(Item $item): never
    {
        abort(404);
    }

    public function storeSingleExtra(SingleExtraRequest $request): RedirectResponse
    {
        $validatedData = $this->itemContributionValidator->validateSingleExtra($request);

        return $this->itemContributionService->storeSingleExtra(
            $validatedData['proprietary'],
            $validatedData['extra']
        );
    }
}
