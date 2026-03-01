<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\BuildsAdminIndexQuery;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Catalog\SingleExtraRequest;
use App\Models\Catalog\Extra;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminExtraController extends AdminBaseController
{
    use BuildsAdminIndexQuery;
    use LocksSubject;

    /** @var array{baseTable: string, searchBaseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
    private const INDEX_CONFIG = [
        'baseTable' => 'extras',
        'searchBaseTable' => 'items',
        'searchSpecial' => [
            'collaborator_id' => ['table' => 'collaborators', 'column' => 'contact'],
            'item_id' => ['table' => 'items', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'collaborator_id' => 'collaborators.contact',
            'item_id' => 'items.name',
        ],
    ];

    public function index(Request $request): View
    {
        $count = Extra::count();
        $query = Extra::query();
        $query
            ->leftJoin('collaborators', 'extras.collaborator_id', '=', 'collaborators.id')
            ->leftJoin('items', 'extras.item_id', '=', 'items.id')
            ->select([
                'extras.*',
                'items.name AS item_name',
                'collaborators.contact AS collaborator_contact',
            ]);

        $this->applyIndexSearch($query, $request->search_column, $request->search, self::INDEX_CONFIG);
        $this->applyIndexSort($query, $request->sort, $request->order, self::INDEX_CONFIG);

        $extras = $query->paginate(30)->withQueryString();

        return view('admin.catalog.extras.index', compact('extras', 'count'));
    }

    public function show(string $id): View
    {
        $extra = Extra::findOrFail($id);

        return view('admin.catalog.extras.show', compact('extra'));
    }

    public function create(): View
    {
        $sections = ItemCategory::orderBy('name', 'asc')->get();
        $collaborators = Collaborator::orderBy('contact', 'asc')->get();

        return view('admin.catalog.extras.create', compact('collaborators', 'sections'));
    }

    public function store(SingleExtraRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $extra = Extra::create($data);

        return redirect()->route('admin.extras.show', $extra)->with('success', __('app.catalog.extra.created'));
    }

    public function edit(string $id): View
    {
        $extra = Extra::findOrFail($id);
        $this->requireUnlocked($extra);

        $collaborators = Collaborator::orderBy('contact', 'asc')->get();
        $sections = ItemCategory::orderBy('name', 'asc')->get();

        $this->lock($extra);

        return view('admin.catalog.extras.edit', compact('extra', 'sections', 'collaborators'));
    }

    public function update(SingleExtraRequest $request, Extra $extra): RedirectResponse
    {
        $this->requireUnlocked($extra);

        $data = $request->validated();

        $extra->update($data);

        $this->unlock($extra);

        return redirect()->route('admin.extras.show', $extra)->with('success', __('app.catalog.extra.updated'));
    }

    public function destroy(Extra $extra): RedirectResponse
    {
        $this->requireUnlocked($extra);

        $this->unlock($extra);

        $extra->delete();

        return redirect()->route('admin.extras.index')->with('success', __('app.catalog.extra.deleted'));
    }
}
