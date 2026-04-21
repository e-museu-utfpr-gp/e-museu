<?php

declare(strict_types=1);

return [
    'shared' => [
        'buttons' => [
            'add' => 'Add',
            'view' => 'View',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'validate_invalidate' => 'Validate / Invalidate',
            'submit' => 'Submit',
            'search' => 'Search',
            'reset' => 'Reset',
            'show_password' => 'Show password',
            'hide_password' => 'Hide password',
        ],
        'yes' => 'Yes',
        'no' => 'No',
        'select_search_placeholder' => 'Search…',
        'select_no_results' => 'No results',
        'info_popover_label' => 'More information',
        'modal_dismiss' => 'Close dialog',
        'languages' => [
            'universal_tooltip_short' => 'About the Universal content language',
            'universal_tooltip' => 'Universal is a special catalog language for text that should read the same for every visitor, no matter the site language (for example institution names, brands, or titles you do not want translated). When the site shows an item, it prefers the visitor’s language, then may use Universal if that locale has no text, before trying other languages. Leave Universal empty if you do not need a single shared version.',
        ],
        'images_upload' => [
            'cover_label' => 'Cover image',
            'cover_help' => 'Main image of the item (required). It will be displayed as the cover.',
            'gallery_label' => 'More images',
            'gallery_help' => 'Optional. Add other images of the item for the gallery.',
            'cover_drop_here' => 'Drag the cover image here or click to choose',
            'cover_required' => 'The cover image is required.',
            'gallery_drop_here' => 'Drag images here or click to add multiple',
            'replace_image' => 'Replace',
            'images_preview_title' => 'Added images',
            'images_preview_empty' => 'No images selected. Add the cover and, if you wish, more images above.',
        ],
    ],
    'catalog' => require __DIR__ . '/view/catalog.php',
    'home' => require __DIR__ . '/view/home.php',
    'layout' => require __DIR__ . '/view/layout.php',
    'about' => require __DIR__ . '/view/about.php',
    'admin' => [
        'catalog' => require __DIR__ . '/view/admin/catalog.php',
        'taxonomy' => require __DIR__ . '/view/admin/taxonomy.php',
        'identity' => require __DIR__ . '/view/admin/identity.php',
        'auth' => require __DIR__ . '/view/admin/auth.php',
        'layout' => require __DIR__ . '/view/admin/layout.php',
        'collaborator' => require __DIR__ . '/view/admin/collaborator.php',
    ],
];
