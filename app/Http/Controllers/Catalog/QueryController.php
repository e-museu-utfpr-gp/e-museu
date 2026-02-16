<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Item;
use App\Models\Proprietary\Proprietary;
use App\Models\Taxonomy\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function tagNameAutoComplete(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $category = $request->input('category');

        $data = Tag::select('name')
            ->where('category_id', 'LIKE', $category)
            ->where('validation', true);

        if ($query !== null && $query !== '') {
            $data = $data->where('name', 'LIKE', '%' . $query . '%');
        }

        $data = $data->limit(10)->get();

        return response()->json($data);
    }

    public function componentNameAutoComplete(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $category = $request->input('category');

        $data = Item::select('name')
            ->where('section_id', 'LIKE', $category)
            ->where('validation', true);

        if ($query !== null && $query !== '') {
            $data = $data->where('name', 'LIKE', '%' . $query . '%');
        }

        $data = $data->limit(10)->get();

        return response()->json($data);
    }

    public function checkTagName(Request $request): JsonResponse
    {
        $data = Tag::where('category_id', 'LIKE', $request->input('category'))
            ->where('name', 'LIKE', $request->input('name'))
            ->where('validation', true)
            ->count();

        return response()->json($data);
    }

    public function checkComponentName(Request $request): JsonResponse
    {
        $data = Item::where('section_id', 'LIKE', $request->input('category'))
            ->where('name', 'LIKE', $request->input('name'))
            ->where('validation', true)
            ->count();

        return response()->json($data);
    }

    /**
     * @return Proprietary|false
     */
    public function checkContact(Request $request)
    {
        $data = Proprietary::where('contact', 'LIKE', $request->input('contact'))
            ->where('blocked', false)
            ->where('is_admin', false)
            ->first();

        if ($data) {
            return $data;
        }

        return false;
    }

    public function getTags(Request $request): JsonResponse
    {
        $data = Tag::where('category_id', 'LIKE', $request->input('category'))
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($data);
    }

    public function getItems(Request $request): JsonResponse
    {
        $data = Item::where('section_id', 'LIKE', $request->input('section'))
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($data);
    }
}
