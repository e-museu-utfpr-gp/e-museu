<?php

return [
    'shared' => [
        'buttons' => [
            'add' => 'Adicionar',
            'view' => 'Visualizar',
            'edit' => 'Editar',
            'delete' => 'Excluir',
            'validate_invalidate' => 'Validar / Invalidar',
            'submit' => 'Enviar',
            'search' => 'Buscar',
        ],
        'yes' => 'Sim',
        'no' => 'Não',
        'info_popover_label' => 'Mais informações',
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
