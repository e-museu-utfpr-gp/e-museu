<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckLock;
use Illuminate\Http\Request;
use App\Http\Requests\SectionRequest;
use App\Models\Section;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminSectionController extends AdminBaseController
{
    public function __construct()
    {
        $this->middleware(CheckLock::class)->only(['edit', 'update', 'destroy']);
    }

    public function index(Request $request): View
    {
        $query = Section::query();
        $count = Section::count();

        if ($request->search_column && $request->search) {
            $query->where($request->search_column, 'LIKE', "%{$request->search}%");
        }

        if ($request->sort && $request->order) {
            if ($request->order === 'asc') {
                $query->orderBy($request->sort, 'desc');
            } else {
                $query->orderBy($request->sort, 'asc');
            }
        }

        $sections = $query->paginate(50)->withQueryString();

        return view('admin.sections.index', compact('sections', 'count'));
    }

    public function show(string $id): View
    {
        $section = Section::find($id);

        return view('admin.sections.show', compact('section'));
    }

    public function create(): View
    {
        return view('admin.sections.create');
    }

    public function store(SectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $section = Section::create($data);

        return redirect()->route('admin.sections.show', $section)->with('success', 'Seção adicionada com sucesso.');
    }

    public function edit(string $id): View
    {
        $section = Section::findOrFail($id);

        $this->lock($section);

        return view('admin.sections.edit', compact('section'));
    }

    public function update(SectionRequest $request, Section $section): RedirectResponse
    {
        $data = $request->validated();

        $section->update($data);

        $this->unlock($section);

        return redirect()->route('admin.sections.show', $section)->with('success', 'Seção atualizada com sucesso.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $this->unlock($section);

        $section->delete();

        return redirect()->route('admin.sections.index')->with('success', 'Seção excluída com sucesso.');
    }
}
