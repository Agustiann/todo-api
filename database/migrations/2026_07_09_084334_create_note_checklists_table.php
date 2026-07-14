<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('note_id')
                ->constrained('notes')
                ->cascadeOnDelete();

            $table->boolean('is_completed')->default(false);
            $table->string('content');
            $table->integer('position')->default(0);

            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users');

            $table->foreignUuid('updated_by')
                ->nullable()
                ->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_checklists');
    }
};