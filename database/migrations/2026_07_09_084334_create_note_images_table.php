<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_images', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('note_id')
                ->constrained('notes')
                ->cascadeOnDelete();

            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');

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
        Schema::dropIfExists('note_images');
    }
};