<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 5)->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('locations')->insert([
            ['id' => 1, 'name' => 'Undefined', 'code' => 'INDEF', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'UTFPR', 'code' => 'UTFPR', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Unicentro', 'code' => 'UNCEN', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::statement('ALTER TABLE locations AUTO_INCREMENT = 4');

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('identification_code');
            $table->boolean('validation')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->nullOnDelete();
            $table->foreignId('collaborator_id')->nullable()->constrained('collaborators')->nullOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('locations');
    }
};
