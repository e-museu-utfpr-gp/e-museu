<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\Taxonomy\TagService;
use App\Support\Http\OptionalContentLocale;
use Illuminate\Http\{JsonResponse, Request};

class TagController extends Controller
{
    public function index(Request $request, TagService $tagService): JsonResponse
    {
        $category = (string) ($request->input('category') ?? '');

        return response()->json(
            $tagService->jsonPayloadForPublicCategorySelect($category)
        );
    }

    public function autocomplete(Request $request, TagService $tagService): JsonResponse
    {
        $query = (string) ($request->input('query') ?? '');
        $category = (string) ($request->input('category') ?? '');
        $languageId = OptionalContentLocale::languageIdOrNull($request);

        $tags = $languageId !== null
            ? $tagService->getValidatedNamesForAutocompleteForLanguage($query, $category, $languageId)
            : $tagService->getValidatedNamesForAutocomplete($query, $category);

        return response()->json($tags);
    }

    public function checkName(Request $request, TagService $tagService): JsonResponse
    {
        $category = (string) ($request->input('category') ?? '');
        $name = (string) ($request->input('name') ?? '');
        $languageId = OptionalContentLocale::languageIdOrNull($request);

        $count = $tagService->countValidatedByNameAndCategory($name, $category, $languageId);

        return response()->json($count);
    }
}
