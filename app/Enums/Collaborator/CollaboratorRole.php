<?php

declare(strict_types=1);

namespace App\Enums\Collaborator;

enum CollaboratorRole: string
{
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';
}
