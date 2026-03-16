<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\Taxonomy\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request, TagService $tagService): JsonResponse
    {
        $category = (string) ($request->input('category') ?? '');

        $tags = $tagService->getByCategory($category);

        return response()->json($tags);
    }

    public function autocomplete(Request $request, TagService $tagService): JsonResponse
    {
        $query = (string) ($request->input('query') ?? '');
        $category = (string) ($request->input('category') ?? '');

        $tags = $tagService->getValidatedNamesForAutocomplete($query, $category);

        return response()->json($tags);
    }

    public function checkName(Request $request, TagService $tagService): JsonResponse
    {
        $category = (string) ($request->input('category') ?? '');
        $name = (string) ($request->input('name') ?? '');

        $count = $tagService->countValidatedByNameAndCategory($name, $category);

        return response()->json($count);
    }
}
