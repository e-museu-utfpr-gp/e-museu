<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model class => Factory class (for Factory::guessFactoryNamesUsing)
    | Used when models live in subnamespaces (e.g. App\Models\Catalog\Item).
    |--------------------------------------------------------------------------
    */
    \App\Models\Catalog\Item::class => \Database\Factories\Catalog\ItemFactory::class,
    \App\Models\Catalog\ItemCategory::class => \Database\Factories\Catalog\ItemCategoryFactory::class,
    \App\Models\Catalog\ItemComponent::class => \Database\Factories\Catalog\ItemComponentFactory::class,
    \App\Models\Catalog\Extra::class => \Database\Factories\Catalog\ExtraFactory::class,
    \App\Models\Catalog\ItemTag::class => \Database\Factories\Catalog\ItemTagFactory::class,
    \App\Models\Taxonomy\Tag::class => \Database\Factories\Taxonomy\TagFactory::class,
    \App\Models\Collaborator\Collaborator::class => \Database\Factories\Collaborator\CollaboratorFactory::class,
];
