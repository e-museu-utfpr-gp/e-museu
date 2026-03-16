<?php

namespace Database\Seeders\Taxonomy;

use App\Models\Catalog\ItemTag;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Tag::factory(100)->create();

            ItemTag::factory(50)->create();
        }
    }
}
