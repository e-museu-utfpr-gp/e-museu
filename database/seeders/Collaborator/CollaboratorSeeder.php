<?php

declare(strict_types=1);

namespace Database\Seeders\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Seeder;

class CollaboratorSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Collaborator::create([
                'full_name' => 'UNICENTRO',
                'email' => 'unicentro@unicentro.com',
                'role' => CollaboratorRole::INTERNAL,
                'blocked' => false,
                'last_email_verification_at' => now(),
            ]);

            Collaborator::create([
                'full_name' => 'UTFPR',
                'email' => 'utfpr@utfpr.com',
                'role' => CollaboratorRole::INTERNAL,
                'blocked' => false,
                'last_email_verification_at' => now(),
            ]);

            Collaborator::factory(30)->create();
        }
    }
}
