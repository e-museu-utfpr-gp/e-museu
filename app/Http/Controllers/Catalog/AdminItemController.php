<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Concerns\BuildsAdminIndexQuery;
use App\Http\Middleware\CheckLock;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\Section;
use App\Models\Proprietary\Proprietary;
use App\Models\Taxonomy\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminItemController extends AdminBaseController
{
    use BuildsAdminIndexQuery;

    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'items',
        'searchSpecial' => [
            'proprietary_id' => ['table' => 'proprietaries', 'column' => 'contact'],
            'section_id' => ['table' => 'sections', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'proprietary_id' => 'proprietaries.contact',
            'section_id' => 'sections.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = Item::count();
        $query = Item::query();
        $query->leftJoin('proprietaries', 'items.proprietary_id', '=', 'proprietaries.id');
        $query->leftJoin('sections', 'items.section_id', '=', 'sections.id');
        $query->select([
            'items.*',
            'items.name AS item_name',
            'items.created_at AS item_created',
            'items.updated_at AS item_updated',
            'items.validation AS item_validation',
            DB::raw('LEFT(items.history, 300) as history'),
            DB::raw('LEFT(items.description, 150) as description'),
            DB::raw('LEFT(items.detail, 150) as detail'),
            'sections.name AS section_name',
            'proprietaries.contact AS proprietary_contact',
        ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $items = $query->paginate(30)->withQueryString();

        return view('admin.items.index', compact('items', 'count'));
    }

    public function show(string $id): View
    {
        $item = Item::find($id);

        return view('admin.items.show', compact('item'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('name')->get();
        $proprietaries = Proprietary::orderBy('full_name')->get();

        return view('admin.items.create', compact('proprietaries', 'sections'));
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $item = false;

        $rules = [
            'proprietary_id' => 'required|integer|numeric|exists:proprietaries,id',
            'validation' => 'required|boolean'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if ($request->image) {
            $data['image'] = $request->image->store('items');
        }

        $data['identification_code'] = '000';

        if ($data['date'] === null) {
            $data['date'] = '0001-01-01 00:00:00';
        }

        DB::transaction(function () use ($data, $item) {
            $item = Item::create($data);

            $data['identification_code'] = self::createIdentificationCode($item);

            $item->update($data);
        });

        return redirect()->route('admin.items.show', $item)->with('success', 'Item adicionado com sucesso.');
    }

    public function edit(string $id): View
    {
        $item = Item::findOrFail($id);

        $this->lock($item);

        $sections = Section::orderBy('name')->get();
        $proprietaries = Proprietary::orderBy('full_name')->get();

        return view('admin.items.edit', compact('item', 'sections', 'proprietaries'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $data = $request->validated();

        if ($request->image) {
            $imagePath = $item->image;

            Storage::delete($imagePath);

            $data['image'] = $request->image->store('items');
        } else {
            unset($data['image']);
        }

        if ($data['date'] === null) {
            $data['date'] = '0001-01-01 00:00:00';
        }

        $item->update($data);

        $this->unlock($item);

        return redirect()->route('admin.items.show', $item)->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->unlock($item);

        $imagePath = $item->image;

        Storage::delete($imagePath);
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Item excluído com sucesso.');
    }

    public function createIdentificationCode(Item $item): string
    {
        $sectionModel = Section::findOrFail($item->section_id);
        $section = self::removeAccent($sectionModel->name);

        $words = explode(' ', $section);

        if (count($words) === 1) {
            $words = explode('-', $words[0]);
        }

        $proprietaryCode = '';

        if (count($words) > 1) {
            $section = strtoupper(substr($words[0], 0, 2));
            $section .= strtoupper(substr(end($words), 0, 2));
        } else {
            $section = strtoupper(substr($words[0], 0, 4));
        }

        $proprietary = $item->proprietary;
        if ($proprietary && $proprietary->is_admin) {
            $proprietaryCode = strtoupper($proprietary->full_name);
        } else {
            $proprietaryCode = 'EXT';
        }

        return $proprietaryCode . '_' . $section . '_' . $item->id;
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
