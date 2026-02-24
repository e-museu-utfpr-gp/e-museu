<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Concerns\LocksSubject;
use App\Http\Requests\Catalog\SectionRequest;
use App\Models\Catalog\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSectionController extends AdminBaseController
{
    use LocksSubject;

    public function index(Request $request): View
    {
        $query = Section::query();
        $count = Section::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            $query->orderBy($request->sort, $request->order);
        }

        $sections = $query->paginate(50)->withQueryString();

        return view('admin.catalog.sections.index', compact('sections', 'count'));
    }

    public function show(string $id): View
    {
        $section = Section::findOrFail($id);

        return view('admin.catalog.sections.show', compact('section'));
    }

    public function create(): View
    {
        return view('admin.catalog.sections.create');
    }

    public function store(SectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $section = Section::create($data);

        return redirect()->route('admin.sections.show', $section)->with('success', __('app.catalog.section.created'));
    }

    public function edit(string $id): View
    {
        $section = Section::findOrFail($id);
        $this->requireUnlocked($section);

        $this->lock($section);

        return view('admin.catalog.sections.edit', compact('section'));
    }

    public function update(SectionRequest $request, Section $section): RedirectResponse
    {
        $this->requireUnlocked($section);

        $data = $request->validated();

        $section->update($data);

        $this->unlock($section);

        return redirect()->route('admin.sections.show', $section)->with('success', __('app.catalog.section.updated'));
    }

    public function destroy(Section $section): RedirectResponse
    {
        $this->requireUnlocked($section);

        $this->unlock($section);

        $section->delete();

        return redirect()->route('admin.sections.index')->with('success', __('app.catalog.section.deleted'));
    }
}
