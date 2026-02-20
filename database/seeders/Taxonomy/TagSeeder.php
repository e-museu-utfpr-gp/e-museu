<?php

namespace Database\Seeders\Taxonomy;

use App\Models\Catalog\TagItem;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Tag::factory(100)->create();

            TagItem::factory(50)->create();
        }
    }
}
