<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class ItemContributionValidator
{
    /**
     * @return array{
     *   proprietary: array<string, mixed>,
     *   item: array<string, mixed>,
     *   tags: array<int, array<string, mixed>>,
     *   extras: array<int, array<string, mixed>>,
     *   components: array<int, array<string, mixed>>
     * }
     */
    public function validateStore(Request $request): array
    {
        $proprietaryRequest = new ProprietaryRequest();
        $storeItemRequest = new StoreItemRequest();
        $tagRequest = new TagRequest();
        $extraRequest = new ExtraRequest();
        $componentRequest = new ComponentRequest();

        $proprietary = $request->validate(
            $proprietaryRequest->rules(),
            $proprietaryRequest->messages()
        );
        $item = $request->validate(
            $storeItemRequest->rules(),
            $storeItemRequest->messages()
        );
        $request->validate($tagRequest->rules(), $tagRequest->messages());
        $request->validate($extraRequest->rules(), $extraRequest->messages());
        $request->validate($componentRequest->rules(), $componentRequest->messages());

        return [
            'proprietary' => $proprietary,
            'item' => $item,
            'tags' => (array) $request->tags,
            'extras' => (array) $request->extras,
            'components' => (array) $request->components,
        ];
    }

    /**
     * @return array{proprietary: array<string, mixed>, extra: array<string, mixed>}
     */
    public function validateSingleExtra(SingleExtraRequest $request): array
    {
        $proprietaryRequest = new ProprietaryRequest();
        $proprietary = $request->validate(
            $proprietaryRequest->rules(),
            $proprietaryRequest->messages()
        );
        $extra = $request->validated();
        $extra['validation'] = 0;

        return ['proprietary' => $proprietary, 'extra' => $extra];
    }
}
