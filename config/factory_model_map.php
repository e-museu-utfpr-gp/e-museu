<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model class => Factory class (for Factory::guessFactoryNamesUsing)
    | Used when models live in subnamespaces (e.g. App\Models\Catalog\Item).
    |--------------------------------------------------------------------------
    */
    \App\Models\Catalog\Item::class => \Database\Factories\Catalog\ItemFactory::class,
    \App\Models\Catalog\Section::class => \Database\Factories\Catalog\SectionFactory::class,
    \App\Models\Catalog\ItemComponent::class => \Database\Factories\Catalog\ItemComponentFactory::class,
    \App\Models\Catalog\Extra::class => \Database\Factories\Catalog\ExtraFactory::class,
    \App\Models\Catalog\TagItem::class => \Database\Factories\Taxonomy\TagItemFactory::class,
    \App\Models\Taxonomy\Tag::class => \Database\Factories\Taxonomy\TagFactory::class,
    \App\Models\Proprietary\Proprietary::class => \Database\Factories\Proprietary\ProprietaryFactory::class,
];
