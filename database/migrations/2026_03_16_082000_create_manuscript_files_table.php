<?php

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
        Schema::create('manuscript_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('manuscript_id');
            $table->string('file_type');
            $table->string('path');
            $table->timestamps();

            $table->foreign('manuscript_id')
                ->references('id')
                ->on('manuscripts')
                ->onDelete('cascade');
            $table->index('manuscript_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscript_files');
    }
};

