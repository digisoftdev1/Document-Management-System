<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->nullable()->constrained('folders')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('total_folders')->default(0);
            $table->unsignedBigInteger('total_files')->default(0);
            $table->unsignedBigInteger('total_storage')->default(0);

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->index(['parent_id', 'name']);
            $table->unique(['parent_id', 'name'], 'folders_parent_name_unique');

            $table->fullText('name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};