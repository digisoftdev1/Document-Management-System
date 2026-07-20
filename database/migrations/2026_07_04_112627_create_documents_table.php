<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();

            $table->foreignId('government_level_id')->constrained()->cascadeOnDelete();

            $table->foreignId('budget_type_id')->constrained()->cascadeOnDelete();

            $table->foreignId('month_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('day');

            $table->string('file_number');

            $table->string('document_name');

            $table->string('pdf_path')->nullable();

            $table->unsignedSmallInteger('total_pages')->default(0);

            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
