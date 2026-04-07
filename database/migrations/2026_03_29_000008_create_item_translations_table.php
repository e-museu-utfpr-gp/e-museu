<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->text('history')->nullable();
            $table->text('detail')->nullable();
            $table->timestamps();
            $table->unique(['item_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_translations');
    }
};
