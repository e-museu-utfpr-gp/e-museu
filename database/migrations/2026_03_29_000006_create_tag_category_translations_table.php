<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_category_id')->constrained('tag_categories')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['tag_category_id', 'language_id']);
        });

        $ptId = DB::table('languages')->where('code', 'pt_BR')->value('id');
        $enId = DB::table('languages')->where('code', 'en')->value('id');
        if ($ptId === null || $enId === null) {
            throw new RuntimeException('Expected pt_BR and en rows in languages (see 2026_03_29_000000_create_languages_table).');
        }

        /** @see 2026_03_29_000005_create_tag_categories_table fixed ids: 1 = brand, 2 = series */
        $labelsByCategoryId = [
            1 => ['pt_BR' => 'Marca', 'en' => 'Brand'],
            2 => ['pt_BR' => 'Série', 'en' => 'Series'],
        ];

        $now = now();
        foreach ($labelsByCategoryId as $categoryId => $namesByCode) {
            foreach ($namesByCode as $code => $name) {
                $languageId = DB::table('languages')->where('code', $code)->value('id');
                if ($languageId === null) {
                    continue;
                }
                DB::table('tag_category_translations')->insert([
                    'tag_category_id' => $categoryId,
                    'language_id' => $languageId,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_category_translations');
    }
};
