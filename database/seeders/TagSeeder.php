<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tag;
use App\Models\TagItem;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        if(app()->environment('local')) {
            Tag::factory(100)->create();

            TagItem::factory(50)->create();
        }
    }
}
