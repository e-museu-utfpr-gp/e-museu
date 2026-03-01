<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route prefix to lockable model (used by LocksSubject trait and ReleaseLockController)
    | Key: route name prefix (e.g. admin.items)
    | Value: [route parameter name, model class]
    |--------------------------------------------------------------------------
    */
    'admin.items' => ['item', \App\Models\Catalog\Item::class],
    'admin.tags' => ['tag', \App\Models\Taxonomy\Tag::class],
    'admin.tag-categories' => ['tag_category', \App\Models\Taxonomy\TagCategory::class],
    'admin.collaborators' => ['collaborator', \App\Models\Collaborator\Collaborator::class],
    'admin.extras' => ['extra', \App\Models\Catalog\Extra::class],
    'admin.item-categories' => ['item_category', \App\Models\Catalog\ItemCategory::class],
];
