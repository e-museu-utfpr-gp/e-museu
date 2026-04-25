<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Http\Requests\Collaborator\PublicCollaboratorRules;
use App\Http\Requests\Taxonomy\TagRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Orchestrates multiple rule sets against a raw {@see Request} (public contribution).
 *
 * Collaborator fields use {@see PublicCollaboratorRules::rules()}. Other nested
 * {@see \Illuminate\Foundation\Http\FormRequest} instances are only used for
 * {@see \Illuminate\Foundation\Http\FormRequest::rules()} and
 * {@see \Illuminate\Foundation\Http\FormRequest::messages()}. The full FormRequest lifecycle
 * does not run here — keep those classes free of required side effects, or duplicate logic here.
 */
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
        self::mergeTrimmedTagNamesIntoRequest($request);

        $storeItemRequest = new StoreItemRequest();
        $tagRequest = new TagRequest();
        $extraRequest = new ExtraRequest();
        $collaborator = $request->validate(PublicCollaboratorRules::rules());
        $item = $request->validate(
            $storeItemRequest->rules(),
            $storeItemRequest->messages()
        );
        // `Validator::validated()` omits keys missing from the request body; optional `date`
        // may be absent when empty. Always take it from input so a sent value is never dropped.
        if (! array_key_exists('date', $item)) {
            $item['date'] = $request->input('date');
        }
        $request->validate($tagRequest->rules(), $tagRequest->messages());
        $request->validate($extraRequest->rules(), $extraRequest->messages());
        $componentRequest = ComponentRequest::createFrom($request);
        $request->validate($componentRequest->rules(), $componentRequest->messages());

        return [
            'collaborator' => $collaborator,
            'item' => $item,
            'tags' => self::whitelistNestedRows(
                (array) $request->input('tags', []),
                self::TAG_ROW_KEYS
            ),
            'extras' => self::whitelistNestedRows(
                (array) $request->input('extras', []),
                self::EXTRA_ROW_KEYS
            ),
            'components' => self::whitelistNestedRows(
                (array) $request->input('components', []),
                self::COMPONENT_ROW_KEYS
            ),
        ];
    }

    /**
     * @return array{collaborator: array<string, mixed>, extra: array<string, mixed>}
     */
    public function validateSingleExtra(SingleExtraRequest $request): array
    {
        $validated = $request->validated();
        $collaborator = Arr::only($validated, ['full_name', 'email']);
        $extra = Arr::only($validated, [
            'content_locale',
            'info',
            'item_id',
            'collaborator_id',
        ]);
        $extra['validation'] = false;

        return ['collaborator' => $collaborator, 'extra' => $extra];
    }

    private static function mergeTrimmedTagNamesIntoRequest(Request $request): void
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
    private static function whitelistNestedRows(array $rows, array $allowedKeys): array
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
}
