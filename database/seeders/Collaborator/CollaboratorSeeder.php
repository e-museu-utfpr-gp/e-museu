<?php

namespace Database\Seeders\Collaborator;

use App\Enums\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Seeder;

class CollaboratorSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Collaborator::create([
                'full_name' => 'UNICENTRO',
                'contact' => 'unicentro@unicentro.com',
                'role' => CollaboratorRole::INTERNAL,
                'blocked' => false,
            ]);

            Collaborator::create([
                'full_name' => 'UTFPR',
                'contact' => 'utfpr@utfpr.com',
                'role' => CollaboratorRole::INTERNAL,
                'blocked' => false,
            ]);

            Collaborator::factory(30)->create();
        }
    }
}
