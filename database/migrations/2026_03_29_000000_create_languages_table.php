<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Allowed `languages.code` values align with {@see \App\Enums\Content\ContentLanguage} (app layer).
     * Explicit ids: 1 = neutral, 2 = pt_BR, 3 = en (display order in UI is still enforced in PHP, not by id).
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('languages')->insert([
            ['id' => 1, 'code' => 'neutral', 'name' => 'Neutral', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'code' => 'pt_BR', 'name' => 'Português (Brasil)', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'code' => 'en', 'name' => 'English', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
