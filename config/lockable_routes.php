<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route prefix to lockable model (for CheckLock middleware)
    | Key: route name prefix (e.g. admin.items)
    | Value: [route parameter name, model class]
    |--------------------------------------------------------------------------
    */
    'admin.items' => ['item', \App\Models\Catalog\Item::class],
    'admin.tags' => ['tag', \App\Models\Taxonomy\Tag::class],
    'admin.categories' => ['category', \App\Models\Taxonomy\Category::class],
    'admin.proprietaries' => ['proprietary', \App\Models\Proprietary\Proprietary::class],
    'admin.extras' => ['extra', \App\Models\Catalog\Extra::class],
    'admin.sections' => ['section', \App\Models\Catalog\Section::class],
];
