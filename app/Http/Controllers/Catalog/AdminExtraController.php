<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Concerns\BuildsAdminIndexQuery;
use App\Http\Middleware\CheckLock;
use Illuminate\Http\Request;
use App\Http\Requests\SingleExtraRequest;
use App\Models\Catalog\Extra;
use App\Models\Catalog\Section;
use App\Models\Proprietary\Proprietary;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminExtraController extends AdminBaseController
{
    use BuildsAdminIndexQuery;

    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

    /** @var array{baseTable: string, searchBaseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'extras',
        'searchBaseTable' => 'items',
        'searchSpecial' => [
            'proprietary_id' => ['table' => 'proprietaries', 'column' => 'contact'],
            'item_id' => ['table' => 'items', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'proprietary_id' => 'proprietaries.contact',
            'item_id' => 'items.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = Extra::count();
        $query = Extra::query();
        $query
            ->leftJoin('proprietaries', 'extras.proprietary_id', '=', 'proprietaries.id')
            ->leftJoin('items', 'extras.item_id', '=', 'items.id')
            ->select([
                'extras.*',
                'items.name AS item_name',
                'proprietaries.contact AS proprietary_contact',
            ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $extras = $query->paginate(30)->withQueryString();

        return view('admin.extras.index', compact('extras', 'count'));
    }

    public function show(string $id): View
    {
        $extra = Extra::find($id);

        return view('admin.extras.show', compact('extra'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('name', 'asc')->get();
        $proprietaries = Proprietary::orderBy('contact', 'asc')->get();

        return view('admin.extras.create', compact('proprietaries', 'sections'));
    }

    public function store(SingleExtraRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $extra = Extra::create($data);

        $message = 'Curiosidade extra adicionada com sucesso.';

        return redirect()->route('admin.extras.show', $extra)->with('success', $message);
    }

    public function edit(string $id): View
    {
        $extra = Extra::findOrFail($id);
        $proprietaries = Proprietary::orderBy('contact', 'asc')->get();
        $sections = Section::orderBy('name', 'asc')->get();

        $this->lock($extra);

        return view('admin.extras.edit', compact('extra', 'sections', 'proprietaries'));
    }

    public function update(SingleExtraRequest $request, Extra $extra): RedirectResponse
    {
        $data = $request->validated();

        $extra->update($data);

        $this->unlock($extra);

        $message = 'Curiosidade extra atualizada com sucesso.';

        return redirect()->route('admin.extras.show', $extra)->with('success', $message);
    }

    public function destroy(Extra $extra): RedirectResponse
    {
        $this->unlock($extra);

        $extra->delete();
        return redirect()->route('admin.extras.index')->with('success', 'Curiosidade extra excluída com sucesso.');
    }
}
