<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;
use App\Models\Proprietary;

class QueryController extends Controller
{
    public function tagNameAutoComplete(Request $request)
    {
        $query = $request->get('query', '');
        $category = $request->get('category');
        
        $data = Tag::select('name')
                    ->where('category_id', 'LIKE', $category)
                    ->where('validation', true);

        if (!empty($query)) {
            $data = $data->where('name', 'LIKE', '%' . $query . '%');
        }
        
        $data = $data->limit(10)->get();

        return response()->json($data);
    }

    public function componentNameAutoComplete(Request $request)
    {
        $query = $request->get('query', '');
        $category = $request->get('category');
        
        $data = Item::select('name')
                    ->where('section_id', 'LIKE', $category)
                    ->where('validation', true);
        
        if (!empty($query)) {
            $data = $data->where('name', 'LIKE', '%' . $query . '%');
        }
        
        $data = $data->limit(10)->get();

        return response()->json($data);
    }

    public function checkTagName(Request $request)
    {
        $data = Tag::where('category_id', 'LIKE', $request->get('category'))
                    ->where('name', 'LIKE', $request->get('name'))
                    ->where('validation', true)
                    ->count();

        return response()->json($data);
    }

    public function checkComponentName(Request $request)
    {
        $data = Item::where('section_id', 'LIKE', $request->get('category'))
                    ->where('name', 'LIKE', $request->get('name'))
                    ->where('validation', true)
                    ->count();

        return response()->json($data);
    }

    public function checkContact(Request $request)
    {
        $data = Proprietary::where('contact', 'LIKE', $request->get('contact'))
                            ->where('blocked', false)
                            ->where('is_admin', false)
                            ->first();

        if ($data) {
            return $data;
        }

        return false;
    }

    public function getTags(Request $request)
    {
        $data = Tag::where('category_id', 'LIKE', $request->get('category'))
                    ->orderBy('name', 'asc')
                    ->get();

        return response()->json($data);
    }

    public function getItems(Request $request)
    {
        $data = Item::where('section_id', 'LIKE', $request->get('section'))
                    ->orderBy('name', 'asc')
                    ->get();

        return response()->json($data);
    }

}
