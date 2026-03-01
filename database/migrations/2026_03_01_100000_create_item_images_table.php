<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the item_images table: one-to-many with items. Each row stores one image
     * (path, type: cover|gallery, sort_order). Data migration from items.image is done
     * in a separate migration.
     */
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('path');
            $table->string('type'); // 'cover' | 'gallery'
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['item_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
};
