<?php

namespace App\Enums\Collaborator;

enum CollaboratorRole: string
{
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';
}
