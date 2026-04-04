<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\Collaborator\CollaboratorRequest;
use App\Http\Requests\Taxonomy\TagRequest;
use Illuminate\Http\Request;

class ItemContributionValidator
{
    /** @var list<string> */
    private const TAG_ROW_KEYS = ['category_id', 'tag_category_id', 'name'];

    /** @var list<string> */
    private const EXTRA_ROW_KEYS = ['info', 'collaborator_id', 'item_id'];

    /** @var list<string> */
    private const COMPONENT_ROW_KEYS = ['item_id'];

    /**
     * @return array{
     *   collaborator: array<string, mixed>,
     *   item: array<string, mixed>,
     *   tags: array<int, array<string, mixed>>,
     *   extras: array<int, array<string, mixed>>,
     *   components: array<int, array<string, mixed>>
     * }
     */
    public function validateStore(Request $request): array
    {
        $this->mergeTrimmedTagNamesIntoRequest($request);

        $collaboratorRequest = new CollaboratorRequest();
        $storeItemRequest = new StoreItemRequest();
        $tagRequest = new TagRequest();
        $extraRequest = new ExtraRequest();
        $collaborator = $request->validate(
            $collaboratorRequest->rules(),
            $collaboratorRequest->messages()
        );
        $item = $request->validate(
            $storeItemRequest->rules(),
            $storeItemRequest->messages()
        );
        $request->validate($tagRequest->rules(), $tagRequest->messages());
        $request->validate($extraRequest->rules(), $extraRequest->messages());
        $componentRequest = ComponentRequest::createFrom($request);
        $request->validate($componentRequest->rules(), $componentRequest->messages());

        return [
            'collaborator' => $collaborator,
            'item' => $item,
            'tags' => $this->whitelistNestedRows(
                (array) $request->input('tags', []),
                self::TAG_ROW_KEYS
            ),
            'extras' => $this->whitelistNestedRows(
                (array) $request->input('extras', []),
                self::EXTRA_ROW_KEYS
            ),
            'components' => $this->whitelistNestedRows(
                (array) $request->input('components', []),
                self::COMPONENT_ROW_KEYS
            ),
        ];
    }

    /** Normalizes `tags.*.name` before {@see \App\Http\Requests\Taxonomy\TagRequest} rules run (avoids logical duplicates with surrounding spaces). */
    private function mergeTrimmedTagNamesIntoRequest(Request $request): void
    {
        $tags = (array) $request->input('tags', []);
        foreach ($tags as $k => $row) {
            if (is_array($row) && array_key_exists('name', $row)) {
                $tags[$k]['name'] = trim((string) $row['name']);
            }
        }
        $request->merge(['tags' => $tags]);
    }

    /**
     * @param  array<int, mixed>  $rows
     * @param  list<string>  $allowedKeys
     * @return list<array<string, mixed>>
     */
    private function whitelistNestedRows(array $rows, array $allowedKeys): array
    {
        $flip = array_flip($allowedKeys);
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $out[] = array_intersect_key($row, $flip);
        }

        return $out;
    }

    /**
     * @return array{collaborator: array<string, mixed>, extra: array<string, mixed>}
     */
    public function validateSingleExtra(SingleExtraRequest $request): array
    {
        $collaboratorRequest = new CollaboratorRequest();
        $collaborator = $request->validate(
            $collaboratorRequest->rules(),
            $collaboratorRequest->messages()
        );
        $extra = $request->validated();
        $extra['validation'] = 0;

        return ['collaborator' => $collaborator, 'extra' => $extra];
    }
}
