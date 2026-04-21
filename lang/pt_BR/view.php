<?php

declare(strict_types=1);

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
            'reset' => 'Limpar',
            'show_password' => 'Mostrar senha',
            'hide_password' => 'Ocultar senha',
        ],
        'yes' => 'Sim',
        'no' => 'Não',
        'select_search_placeholder' => 'Pesquisar…',
        'select_no_results' => 'Nenhum resultado',
        'info_popover_label' => 'Mais informações',
        'modal_dismiss' => 'Fechar diálogo',
        'languages' => [
            'universal_tooltip_short' => 'Sobre o idioma Universal no conteúdo',
            'universal_tooltip' => 'Universal é um idioma especial do catálogo para textos que devem aparecer iguais para todos os visitantes, independentemente do idioma do site (por exemplo nomes de instituições, marcas ou títulos que você não quer traduzir). Na exibição, o site prefere o idioma do visitante e pode usar Universal se aquele idioma não tiver texto, antes de tentar outros. Deixe Universal vazio se não precisar de uma única versão compartilhada.',
        ],
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
