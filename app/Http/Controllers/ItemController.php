<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\DifferentIds;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\TagRequest;
use App\Http\Requests\ExtraRequest;
use App\Http\Requests\ComponentRequest;
use App\Http\Requests\SingleExtraRequest;
use App\Http\Requests\ProprietaryRequest;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Proprietary;
use App\Models\Extra;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Section;
use App\Models\ItemComponent;
use App\Models\Contribution;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();
        $query->select('id', 'name', 'date', 'section_id', 'description', 'identification_code', 'image');
        $query->where('validation', true);
        $order = $request->order;
        $sectionName = '';
        $section_id = $request->section;

        if (!$order)
            $order= 1;

        if (!$section_id)
            $section_id = request()->input('section');

        if ($section_id)
            $query->where('section_id', $section_id);

        if (isset($request->search) && ($request->search != null))
            $query->where('name', 'LIKE', "%{$request->search}%");

        if (isset($request->category) && ($request->category != null))
        {
            $query->whereHas('tags', function ($query) use ($request) {
                $query->whereIn('category_id', $request->category)
                        ->where('tag_item.validation', true);
            });
        }

        if (isset($request->tag) && ($request->tag != null))
        {
            $query->whereHas('tags', function ($query) use ($request) {
                $query->whereIn('tag_id', $request->tag)
                        ->where('tag_item.validation', true);
            });
        }

        switch ($order) {
            case 1:
                $query->orderBy('date', 'asc');
                break;
            case 2:
                $query->orderBy('date', 'desc');
                break;
            case 3:
                $query->orderBy('name', 'asc');
                break;
            case 4:
                $query->orderBy('name', 'desc');
                break;
        }

        if ($section_id)
            $sectionName = Section::find($section_id)->name;

        $items = $query->paginate(24)->withQueryString()->appends(['section' => $section_id]);

        $sections = self::loadSections();
        $categories = self::loadCategories();

        return view('items/index', compact('items', 'sections', 'categories', 'sectionName'));
    }

    public function create()
    {
        $categories = category::all();
        $sections = section::all();

        return view('items/create', compact('categories', 'sections'));
    }

    public function store(request $request)
    {
        $proprietaryRequest = new ProprietaryRequest();
        $itemRequest = new StoreItemRequest();
        $tagRequest = new TagRequest();
        $extraRequest = new ExtraRequest();
        $componentRequest = new ComponentRequest();

        $proprietaryData = $request->validate($proprietaryRequest->rules(), $proprietaryRequest->messages());
        $itemData = $request->validate($itemRequest->rules(), $itemRequest->messages());
        $tagData = $request->validate($tagRequest->rules(), $tagRequest->messages());
        $extraData = $request->validate($extraRequest->rules(), $extraRequest->messages());
        $componentData = $request->validate($componentRequest->rules(), $componentRequest->messages());

        $proprietary = Proprietary::where('contact', '=', $proprietaryData['contact'])->first();

        if (!$proprietary)
            $proprietary = self::storeProprietary($proprietaryData);

        if ($proprietary->blocked == 1)
            return back()->withErrors(['Este usuário não possui permissão para registrar itens.']);

        if ($request->image) {
            $itemData['image'] = $request->image->store('items');
        }

        $item = self::storeItem($itemData, $proprietary);

        self::storeMultipleTag($request, $item);

        self::storeMultipleExtra($request, $item, $proprietary);

        self::storeMultipleComponent($request, $item, $componentData);

        return redirect()->route('items.create')->with('success', 'Agradecemos pelo seu tempo! Analisaremos sua colaboração antes de adicionarmos ao nosso museu.');
    }

    public function show($id)
    {
        $item = Item::find($id);
        $sections = Section::get();
        $categories = Category::get();

        return view('items.show', compact('item', 'sections', 'categories'));
    }

    public function edit(item $item)
    {
        //
    }

    public function storeProprietary($proprietaryData)
    {
        $proprietary = Proprietary::create($proprietaryData);

        return $proprietary;
    }

    public function storeItem($itemData, $proprietary)
    {
        $itemData['proprietary_id'] = $proprietary->id;
        $itemData['identification_code'] = '000';

        if ($itemData['date'] === null)
            $itemData['date'] = '0001-01-01 00:00:00';

        $item = DB::transaction(function () use ($itemData){

            $item = Item::create($itemData);

            $itemData['identification_code'] = self::createIdentificationCode($item);

            $item->update($itemData);

            return $item;
        });

        return $item;
    }

    public function storeMultipleTag($request, $item)
    {
        foreach((array) $request->tags as $key => $data) {
            $tag = Tag::where('category_id', '=', $data['category_id'])->where('name', '=', $data['name'])->first();

            if (is_null($tag))
                $tag = Tag::create($data);

            $item->tags()->attach($tag->id);
        }
    }

    public function storeMultipleExtra($request, $item, $proprietary)
    {
        foreach((array) $request->extras as $key => $data) {
            $data['proprietary_id'] = $proprietary->id;
            $data['item_id'] = $item->id;

            $extra = Extra::create($data);
        }
    }

    public function storeMultipleComponent($request, $item, $componentData)
    {
        foreach((array) $request->components as $key => $data) {
            $component = Item::where('section_id', '=', $data['category_id'])
                            ->where('name', '=', $data['name'])
                            ->first();

            $data['component_id'] = $component->id;
            $data['item_id'] = $item->id;

            $extra = ItemComponent::create($data);
        }
    }

    public function loadCategories()
    {
        $data = Category::select('name', 'id')->orderBy('name', 'asc')->get();

        return $data;
    }

    public function loadTags($category)
    {
        $data = Tag::select('name', 'id')->where('validation', true)->where('category_id', $category)->orderBy('name', 'asc')->get();

        return $data;
    }

    public function loadSections()
    {
        $data = Section::select('name', 'id')->orderBy('name', 'asc')->get();

        return $data;
    }

    public function storeSingleExtra(SingleExtraRequest $request)
    {
        $proprietaryRequest = new ProprietaryRequest();
        $proprietaryData = $request->validate($proprietaryRequest->rules(), $proprietaryRequest->messages());

        $data = $request->validated();
        $data['validation'] = 0;

        $proprietary = Proprietary::where('contact', $proprietaryData['contact'])->first();

        if (!$proprietary)
            $proprietary = self::storeProprietary($proprietary);

        if ($proprietary->blocked == 1)
            return back()->withErrors(['Este usuário não possui permissão para registrar itens.']);

        $data['proprietary_id'] = $proprietary->id;

        $extra = Extra::create($data);
        return back()->with('success', 'Curiosidade extra enviada com sucesso! Agradecemos pelo seu tempo, analisaremos sua proposta antes de adicionarmos ao nosso museu.');
    }

    public function createIdentificationCode(Item $item)
    {
        $section = Section::find($item->section_id)->name;
        $section = self::removeAccent($section);

        $words = explode(' ', $section);

        if (count($words) == 1)
            $words = explode('-', $words[0]);

        if (count($words) > 1) {
            $section = strtoupper(substr($words[0], 0, 2));
            $section .= strtoupper(substr(end($words), 0, 2));
        } else {
            $section = strtoupper(substr($words[0], 0, 4));
        }

        $proprietaryCode = 'EXT';

        return $proprietaryCode . '_' . $section . '_' . $item->id;
    }

    public function removeAccent($string)
    {
        return preg_replace(array(
                "/(á|à|ã|â|ä)/",
                "/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/",
                "/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/",
                "/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/",
                "/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/",
                "/(Ú|Ù|Û|Ü)/",
                "/(ñ)/",
                "/(Ñ)/"),
                explode(" ","a A e E i I o O u U n N"),
                $string);
    }
}
