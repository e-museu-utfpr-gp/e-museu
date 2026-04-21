<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        $now = now();
        DB::table('tag_categories')->insert([
            ['id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tag_categories AUTO_INCREMENT = 3');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_categories');
    }
};
