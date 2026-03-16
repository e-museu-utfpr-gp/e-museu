<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\Collaborator\CollaboratorRequest;
use App\Http\Requests\Taxonomy\TagRequest;
use Illuminate\Http\Request;

class ItemContributionValidator
{
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
        $collaboratorRequest = new CollaboratorRequest();
        $storeItemRequest = new StoreItemRequest();
        $tagRequest = new TagRequest();
        $extraRequest = new ExtraRequest();
        $componentRequest = new ComponentRequest();

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
        $request->validate($componentRequest->rules(), $componentRequest->messages());

        return [
            'collaborator' => $collaborator,
            'item' => $item,
            'tags' => (array) $request->tags,
            'extras' => (array) $request->extras,
            'components' => (array) $request->components,
        ];
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
