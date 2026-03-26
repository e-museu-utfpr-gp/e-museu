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
            'show_password' => 'Mostrar senha',
            'hide_password' => 'Ocultar senha',
        ],
        'yes' => 'Sim',
        'no' => 'Não',
        'info_popover_label' => 'Mais informações',
        'images_upload' => [
            'cover_label' => 'Imagem de capa',
            'cover_help' => 'Imagem principal do item (obrigatória). Será exibida como capa.',
            'gallery_label' => 'Mais imagens',
            'gallery_help' => 'Opcional. Adicione outras imagens do item para a galeria.',
            'cover_drop_here' => 'Arraste a imagem de capa aqui ou clique para escolher',
            'cover_required' => 'A imagem de capa é obrigatória.',
            'gallery_drop_here' => 'Arraste imagens aqui ou clique para adicionar várias',
            'replace_image' => 'Trocar',
            'images_preview_title' => 'Imagens adicionadas',
            'images_preview_empty' => 'Nenhuma imagem selecionada. Adicione a capa e, se quiser, mais imagens acima.',
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
