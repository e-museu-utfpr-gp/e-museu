<?php

declare(strict_types=1);

return [
    'collaborator_verification_code' => [
        'subject' => 'Seu código de verificação — :app',
        'greeting' => 'Olá :name,',
        'greeting_generic' => 'Olá,',
        'line1' => 'Use o código abaixo para confirmar seu endereço de e-mail no formulário do museu:',
        'line2' => 'O código expira em 15 minutos. Se você não solicitou isso, ignore este e-mail.',
        'salutation' => 'Atenciosamente, :app',
    ],
    'item_contribution_received' => [
        'subject' => 'Recebemos sua contribuição — :item — :app',
        'greeting' => 'Olá :name,',
        'line1' => 'Recebemos sua contribuição ao catálogo “:item”. Obrigado.',
        'line2' => 'A equipe pode revisar o envio antes de ele aparecer publicamente.',
        'salutation' => 'Atenciosamente, :app',
        'untitled_item' => '(sem título)',
    ],
    'extra_contribution_received' => [
        'subject' => 'Recebemos seu complemento — :item — :app',
        'greeting' => 'Olá :name,',
        'line1' => 'Recebemos suas informações adicionais sobre “:item”. Obrigado.',
        'line2' => 'A equipe pode revisar o envio antes de ele aparecer publicamente.',
        'salutation' => 'Atenciosamente, :app',
    ],
];
