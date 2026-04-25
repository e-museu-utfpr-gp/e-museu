<?php

declare(strict_types=1);

return [
    'tag_category' => [
        'created' => 'Tag category added successfully.',
        'updated' => 'Tag category updated successfully.',
        'deleted' => 'Tag category deleted successfully.',
    ],
    'tag' => [
        'created' => 'Tag added successfully.',
        'updated' => 'Tag updated successfully.',
        'deleted' => 'Tag deleted successfully.',
    ],
    'admin' => [
        'tags' => [
            'index' => [
                'search_option_id' => 'Id',
                'search_option_name' => 'Name',
                'search_option_validation' => 'Validated',
                'search_option_tag_category_id' => 'Category',
                'search_option_created_at' => 'Created at',
                'search_option_updated_at' => 'Updated at',
                'sort_id' => 'Id',
                'sort_name' => 'Name',
                'sort_validation' => 'Validated',
                'sort_tag_category_id' => 'Category',
                'sort_created_at' => 'Created at',
                'sort_updated_at' => 'Updated at',
            ],
        ],
        'tag_categories' => [
            'index' => [
                'search_option_id' => 'Id',
                'search_option_name' => 'Name',
                'search_option_created_at' => 'Created at',
                'search_option_updated_at' => 'Updated at',
                'sort_id' => 'Id',
                'sort_name' => 'Name',
                'sort_created_at' => 'Created at',
                'sort_updated_at' => 'Updated at',
            ],
        ],
    ],
];
