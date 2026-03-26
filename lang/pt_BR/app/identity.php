<?php

return [
    'admin' => [
        'created' => 'Administrador adicionado com sucesso.',
        'deleted' => 'Administrador excluído com sucesso.',
        'admins' => [
            'index' => [
                'search_option_id' => 'Id',
                'search_option_username' => 'Nome de Usuário',
                'search_option_created_at' => 'Criado em',
                'search_option_updated_at' => 'Atualizado em',
                'sort_id' => 'Id',
                'sort_username' => 'Nome de Usuário',
                'sort_created_at' => 'Criado em',
                'sort_updated_at' => 'Atualizado em',
            ],
        ],
    ],
    'lock_blocked' => 'Não é possível fazer alterações enquanto outro administrador estiver editando o mesmo.',
    'lock_removed' => 'Tranca de edição relacionada ao administrador removida com sucesso.',
    'lock_not_found' => 'Nenhuma tranca está associada a este administrador.',
];
