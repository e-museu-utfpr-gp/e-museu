<?php

declare(strict_types=1);

return [
    'access_denied' => 'Acesso negado.',
    'blocked_from_registering' => 'Este usuário não possui permissão para registrar itens.',
    'email_unique' => 'O campo e-mail já está sendo utilizado.',
    'email_reserved_for_internal' => 'Este endereço não pode ser usado aqui. Tente outro e-mail.',
    'email_must_verify_before_contribution' => 'Para cada envio, confirme seu e-mail com o código (botão abaixo); em seguida envie o formulário novamente.',
    'extra_collaborator_email_mismatch' => 'O identificador do colaborador não corresponde a este e-mail. Confirme o código novamente para este endereço.',
    'verify_code_sent' => 'Enviamos um código de 6 dígitos para o seu e-mail.',
    'verify_blocked' => 'Esta conta não pode solicitar verificação.',
    'verify_internal_reserved' => 'Este endereço não pode ser usado aqui. Tente outro e-mail.',
    'verify_mail_not_configured' => 'O envio de e-mail não está configurado. Tente mais tarde ou contate o museu.',
    'verify_mail_send_failed' => 'Não foi possível enviar o e-mail de verificação. Tente mais tarde.',
    'verify_service_unavailable' => 'A verificação está temporariamente indisponível. Tente mais tarde.',
    'verify_confirmed' => 'E-mail confirmado. Você já pode enviar a contribuição.',
    'verify_invalid_code' => 'Código inválido. Verifique e tente de novo.',
    'verify_code_expired' => 'Este código expirou. Solicite um novo.',
    'created' => 'Colaborador adicionado com sucesso.',
    'updated' => 'Colaborador atualizado com sucesso.',
    'deleted' => 'Colaborador excluído com sucesso.',
    'role' => [
        'internal' => 'Interno',
        'external' => 'Externo',
    ],
    'admin' => [
        'collaborators' => [
            'index' => [
                'search_option_id' => 'Id',
                'search_option_full_name' => 'Nome Completo',
                'search_option_email' => 'E-mail',
                'search_option_role' => 'Papel',
                'search_option_blocked' => 'Bloqueado',
                'search_option_last_email_verification_at' => 'Última vez confirmado',
                'search_option_created_at' => 'Criado em',
                'search_option_updated_at' => 'Atualizado em',
                'sort_id' => 'Id',
                'sort_full_name' => 'Nome Completo',
                'sort_email' => 'E-mail',
                'sort_role' => 'Papel',
                'sort_blocked' => 'Bloqueado',
                'sort_last_email_verification_at' => 'Última vez confirmado',
                'sort_created_at' => 'Criado em',
                'sort_updated_at' => 'Atualizado em',
            ],
        ],
    ],
];
