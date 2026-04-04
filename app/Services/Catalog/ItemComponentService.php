<?php

namespace App\Services\Catalog;

use App\Models\Catalog\{Item, ItemComponent};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Support\Admin\{AdminIndexConfig, AdminIndexQueryBuilder};

class ItemComponentService
{
    /**
     * @return array{itemComponents: LengthAwarePaginator<int, ItemComponent>, count: int}
     */
    public function getPaginatedItemComponentsForAdminIndex(Request $request): array
    {
        $count = ItemComponent::count();
        $query = ItemComponent::query()->forAdminList();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::itemComponents());

        $itemComponents = $query->paginate(30)->withQueryString();

        return ['itemComponents' => $itemComponents, 'count' => $count];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createItemComponent(array $data): ItemComponent
    {
        return ItemComponent::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateItemComponent(ItemComponent $itemComponent, array $data): void
    {
        $itemComponent->update($data);
    }

    public function deleteItemComponent(ItemComponent $itemComponent): void
    {
        $itemComponent->delete();
    }

    /**
     * Links catalog items as components of the parent item (each row's `item_id` is the component catalog id).
     * Pivot `validation` stays false until an admin approves.
     *
     * Used only from {@see \App\Actions\Catalog\StoreItemContributionAction} (public contribution flow).
     *
     * @param  array<int, array<string, mixed>>  $componentsData
     */
    public function attachContributedComponents(Item $item, array $componentsData): void
    {
        foreach ($componentsData as $componentItemData) {
            $componentId = (int) ($componentItemData['item_id'] ?? 0);
            if ($componentId <= 0) {
                continue;
            }

            if (! Item::query()->whereKey($componentId)->exists()) {
                throw ValidationException::withMessages([
                    'components' => [__('validation.catalog.component_item_not_found')],
                ]);
            }

            ItemComponent::create([
                'item_id' => $item->id,
                'component_id' => $componentId,
                'validation' => false,
            ]);
        }
    }
}
