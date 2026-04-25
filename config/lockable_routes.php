<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Lockable subject by admin route name prefix (aligned with routes/web.php)
    | Key: prefix of named routes (e.g. admin.catalog.items.edit)
    | Value: [route parameter name, model class]
    |--------------------------------------------------------------------------
    */
    'admin.catalog.items' => ['item', \App\Models\Catalog\Item::class],
    'admin.taxonomy.tags' => ['tag', \App\Models\Taxonomy\Tag::class],
    'admin.taxonomy.tag-categories' => ['tag_category', \App\Models\Taxonomy\TagCategory::class],
    'admin.collaborators' => ['collaborator', \App\Models\Collaborator\Collaborator::class],
    'admin.catalog.extras' => ['extra', \App\Models\Catalog\Extra::class],
    'admin.catalog.item-categories' => ['item_category', \App\Models\Catalog\ItemCategory::class],
];
