<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the item_images table: one-to-many with items. Each row stores one image
     * (path, type: cover|gallery|qrcode, sort_order).
     *
     * The ENUM values below are duplicated on purpose — do not import
     * {@see \App\Enums\Catalog\ItemImageType} here. If the PHP enum changes without a
     * deliberate migration that alters this column, MySQL can reject writes or coerce data.
     * Keep this list identical to `ItemImageType` case values.
     */
    public function up(): void
    {
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->text('path');
            $table->enum('type', ['cover', 'gallery', 'qrcode']);
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
