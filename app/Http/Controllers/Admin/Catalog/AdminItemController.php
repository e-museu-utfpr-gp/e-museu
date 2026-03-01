<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\BuildsAdminIndexQuery;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Catalog\StoreItemRequest;
use App\Http\Requests\Catalog\UpdateItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use RuntimeException;

class AdminItemController extends AdminBaseController
{
    use BuildsAdminIndexQuery;
    use LocksSubject;

    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'items',
        'searchSpecial' => [
            'collaborator_id' => ['table' => 'collaborators', 'column' => 'contact'],
            'category_id' => ['table' => 'item_categories', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'collaborator_id' => 'collaborators.contact',
            'category_id' => 'item_categories.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = Item::count();
        $query = Item::query();
        $query->leftJoin('collaborators', 'items.collaborator_id', '=', 'collaborators.id');
        $query->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id');
        $query->select([
            'items.*',
            'items.name AS item_name',
            'items.created_at AS item_created',
            'items.updated_at AS item_updated',
            'items.validation AS item_validation',
            DB::raw('LEFT(items.history, 300) as history'),
            DB::raw('LEFT(items.description, 150) as description'),
            DB::raw('LEFT(items.detail, 150) as detail'),
            'item_categories.name AS section_name',
            'collaborators.contact AS collaborator_contact',
        ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $items = $query->paginate(30)->withQueryString();

        return view('admin.catalog.items.index', compact('items', 'count'));
    }

    public function show(string $id): View
    {
        $item = Item::findOrFail($id);

        return view('admin.catalog.items.show', compact('item'));
    }

    public function create(): View
    {
        $sections = ItemCategory::orderBy('name')->get();
        $collaborators = Collaborator::orderBy('full_name')->get();

        return view('admin.catalog.items.create', compact('collaborators', 'sections'));
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $item = false;

        $rules = [
            'collaborator_id' => 'required|integer|numeric|exists:collaborators,id',
            'validation' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'history' => $request->input('history'),
            'detail' => $request->input('detail'),
            'date' => $request->input('date') ?? '0001-01-01 00:00:00',
            'category_id' => $request->input('category_id'),
            'collaborator_id' => $request->input('collaborator_id'),
            'validation' => $request->boolean('validation'),
        ];

        $data['identification_code'] = '000';

        DB::transaction(function () use ($data, &$item) {
            $item = Item::create($data);

            $data['identification_code'] = self::createIdentificationCode($item);

            $item->update($data);
        });

        if (! $item instanceof Item) {
            throw new RuntimeException('Item creation failed');
        }

        if ($request->image) {
            $ext = $request->image->getClientOriginalExtension() ?: 'png';
            $path = Item::buildImagePath($item, $ext);
            Storage::disk('public')->put($path, $request->image->get());
            $item->update(['image' => $path]);
        }

        return redirect()->route('admin.items.show', $item)->with('success', __('app.catalog.item.created'));
    }

    public function edit(string $id): View
    {
        $item = Item::findOrFail($id);
        $this->requireUnlocked($item);

        $this->lock($item);

        $sections = ItemCategory::orderBy('name')->get();
        $collaborators = Collaborator::orderBy('full_name')->get();

        return view('admin.catalog.items.edit', compact('item', 'sections', 'collaborators'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $this->requireUnlocked($item);

        $data = $request->validated();

        if ($request->image) {
            $oldPath = $item->getRawOriginal('image');
            if ($oldPath !== null && $oldPath !== '' && ! str_starts_with((string) $oldPath, 'http')) {
                Storage::disk('public')->delete($oldPath);
            }

            $ext = $request->image->getClientOriginalExtension() ?: 'png';
            $data['image'] = Item::buildImagePath($item, $ext);
            Storage::disk('public')->put($data['image'], $request->image->get());
        } else {
            unset($data['image']);
        }

        if ($data['date'] === null) {
            $data['date'] = '0001-01-01 00:00:00';
        }

        $item->update($data);

        $this->unlock($item);

        return redirect()->route('admin.items.show', $item)->with('success', __('app.catalog.item.updated'));
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->requireUnlocked($item);

        $this->unlock($item);

        $imagePath = $item->getRawOriginal('image');
        if ($imagePath !== null && $imagePath !== '' && ! str_starts_with((string) $imagePath, 'http')) {
            Storage::disk('public')->delete($imagePath);
        }

        $itemFolder = 'items/' . $item->id;
        if (Storage::disk('public')->exists($itemFolder)) {
            Storage::disk('public')->deleteDirectory($itemFolder);
        }

        $item->delete();

        return redirect()->route('admin.items.index')->with('success', __('app.catalog.item.deleted'));
    }

    public function createIdentificationCode(Item $item): string
    {
        $sectionModel = ItemCategory::findOrFail($item->category_id);
        $section = self::removeAccent($sectionModel->name);

        $words = explode(' ', $section);

        if (count($words) === 1) {
            $words = explode('-', $words[0]);
        }

        $collaboratorCode = '';
        $collaborator = $item->collaborator;
        if ($collaborator) {
            $initials = collect(explode(' ', $collaborator->full_name))
                ->map(fn (string $w): string => mb_substr($w, 0, 1))
                ->implode('');
            $collaboratorCode = $initials !== '' ? strtoupper($initials) : 'COL';
        } else {
            $collaboratorCode = 'COL';
        }

        if (count($words) > 1) {
            $section = strtoupper(substr($words[0], 0, 2));
            $section .= strtoupper(substr(end($words), 0, 2));
        } else {
            $section = strtoupper(substr($words[0], 0, 4));
        }

        return $collaboratorCode . '_' . $section . '_' . $item->id;
    }

    public function removeAccent(string $string): string
    {
        $result = preg_replace(
            [
                '/(á|à|ã|â|ä)/',
                '/(Á|À|Ã|Â|Ä)/',
                '/(é|è|ê|ë)/',
                '/(É|È|Ê|Ë)/',
                '/(í|ì|î|ï)/',
                '/(Í|Ì|Î|Ï)/',
                '/(ó|ò|õ|ô|ö)/',
                '/(Ó|Ò|Õ|Ô|Ö)/',
                '/(ú|ù|û|ü)/',
                '/(Ú|Ù|Û|Ü)/',
                '/(ñ)/',
                '/(Ñ)/',
            ],
            explode(' ', 'a A e E i I o O u U n N'),
            $string
        );

        return $result ?? $string;
    }
}
