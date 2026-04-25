<?php

declare(strict_types=1);

return [
    'collaborator_verification_code' => [
        'subject' => 'Your verification code — :app',
        'greeting' => 'Hello :name,',
        'greeting_generic' => 'Hello,',
        'line1' => 'Use the code below to confirm your email address in the museum form:',
        'line2' => 'The code expires in 15 minutes. If you did not request this, you can ignore this email.',
        'salutation' => 'Regards, :app',
    ],
    'item_contribution_received' => [
        'subject' => 'We received your contribution — :item — :app',
        'greeting' => 'Hello :name,',
        'line1' => 'We have received your catalog contribution “:item”. Thank you.',
        'line2' => 'The team may review the submission before it appears publicly.',
        'salutation' => 'Regards, :app',
        'untitled_item' => '(untitled)',
    ],
    'extra_contribution_received' => [
        'subject' => 'We received your extra — :item — :app',
        'greeting' => 'Hello :name,',
        'line1' => 'We have received your additional information for “:item”. Thank you.',
        'line2' => 'The team may review the submission before it appears publicly.',
        'salutation' => 'Regards, :app',
    ],
];
