<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained('users');

            $table->foreignUuid('folder_id')
                ->nullable()
                ->constrained('folders')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('content')->nullable();

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
        Schema::dropIfExists('notes');
    }
};