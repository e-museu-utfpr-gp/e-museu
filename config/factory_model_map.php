<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model class => Factory class (for Factory::guessFactoryNamesUsing)
    | Used when models live in subnamespaces (e.g. App\Models\Catalog\Item).
    |--------------------------------------------------------------------------
    */
    \App\Models\Catalog\Item::class => \Database\Factories\ItemFactory::class,
    \App\Models\Catalog\Section::class => \Database\Factories\SectionFactory::class,
    \App\Models\Catalog\ItemComponent::class => \Database\Factories\ItemComponentFactory::class,
    \App\Models\Catalog\Extra::class => \Database\Factories\ExtraFactory::class,
    \App\Models\Catalog\TagItem::class => \Database\Factories\TagItemFactory::class,
    \App\Models\Taxonomy\Tag::class => \Database\Factories\TagFactory::class,
    \App\Models\Proprietary\Proprietary::class => \Database\Factories\ProprietaryFactory::class,
];
