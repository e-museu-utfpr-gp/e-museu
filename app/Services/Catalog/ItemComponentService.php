<?php

namespace App\Services\Catalog;

use App\Models\Catalog\ItemComponent;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

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
}
