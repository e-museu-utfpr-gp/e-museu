<?php

use App\Enums\CollaboratorRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roleValues = array_map(fn (CollaboratorRole $case) => $case->value, CollaboratorRole::cases());

        Schema::create('collaborators', function (Blueprint $table) use ($roleValues): void {
            $table->id();
            $table->string('full_name');
            $table->string('contact');
            $table->enum('role', $roleValues)->default(CollaboratorRole::EXTERNAL->value);
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
