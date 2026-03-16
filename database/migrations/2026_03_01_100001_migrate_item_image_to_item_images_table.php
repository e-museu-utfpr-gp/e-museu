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
     * Migrates existing items.image values into item_images as type 'cover', then drops
     * the items.image column. Only local storage paths are copied; rows whose image
     * value is a URL (e.g. starts with "http") are skipped. Requires item_images table
     * to exist (previous migration).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('items', 'image')) {
            return;
        }

        $rows = DB::table('items')->whereNotNull('image')->where('image', '!=', '')->get(['id', 'image']);
        foreach ($rows as $row) {
            if (str_starts_with((string) $row->image, 'http')) {
                continue;
            }
            DB::table('item_images')->insert([
                'item_id' => $row->id,
                'path' => $row->image,
                'type' => 'cover',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Restores the items.image column, copies back the first cover path per item from
     * item_images (by item_id and sort_order), then truncates item_images. The
     * create_item_images_table migration is responsible for dropping the table.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('image')->nullable()->after('identification_code');
        });

        $covers = DB::table('item_images')->where('type', 'cover')->orderBy('item_id')->orderBy('sort_order')->get(['item_id', 'path']);
        $byItem = $covers->groupBy('item_id');
        foreach ($byItem as $itemId => $images) {
            $first = $images->first();
            DB::table('items')->where('id', $itemId)->update(['image' => $first->path]);
        }

        DB::table('item_images')->truncate();
    }
};
