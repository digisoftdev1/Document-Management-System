<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repository_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('folder_id')->nullable()->constrained()->nullOnDelete();

            $table->string('original_name');
            $table->string('stored_name');

            $table->string('extension', 20)->nullable();
            $table->string('mime_type')->nullable();

            $table->unsignedBigInteger('size')->default(0);

            $table->string('disk')->default('public');
            $table->string('path');

            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            $table->index(['folder_id', 'created_at'], 'repository_files_browse_index');

            $table->fullText('original_name', 'repository_files_name_fulltext');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repository_files');
    }
};