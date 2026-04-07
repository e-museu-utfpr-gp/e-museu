<?php

return [
    'admin' => [
        'created' => 'Administrator added successfully.',
        'deleted' => 'Administrator deleted successfully.',
        'admins' => [
            'index' => [
                'search_option_id' => 'Id',
                'search_option_username' => 'Username',
                'search_option_created_at' => 'Created at',
                'search_option_updated_at' => 'Updated at',
                'sort_id' => 'Id',
                'sort_username' => 'Username',
                'sort_created_at' => 'Created at',
                'sort_updated_at' => 'Updated at',
            ],
        ],
    ],
    'lock_blocked' => 'Changes cannot be made while another administrator is editing the same record.',
    'lock_removed' => 'Edit lock related to the administrator removed successfully.',
    'lock_not_found' => 'No lock is associated with this administrator.',
];
