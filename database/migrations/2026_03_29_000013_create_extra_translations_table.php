<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_id')->constrained('extras')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->text('info');
            $table->timestamps();
            $table->unique(['extra_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_translations');
    }
};
