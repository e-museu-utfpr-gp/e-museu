<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ENUM values are listed literally on purpose — do not import
     * {@see \App\Enums\Collaborator\CollaboratorRole} here. Keep this list identical to the
     * enum case values; new roles require a migration that alters the column.
     */
    public function up(): void
    {
        Schema::create('collaborators', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name');
            $table->string('contact');
            $table->enum('role', ['internal', 'external'])->default('external');
            $table->boolean('blocked')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
