<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemComponent;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        if(app()->environment('local')) {
            Item::factory(100)->create();
            ItemComponent::factory(50)->create();
        }
    }
}
