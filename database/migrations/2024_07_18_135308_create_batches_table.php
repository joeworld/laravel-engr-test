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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name'); // Name of the provider
            $table->string('batch_name'); // Unique batch identifier (e.g., "Provider A Jan 2021")
            $table->unsignedTinyInteger('batch_month'); // Month of the batch
            $table->unsignedSmallInteger('batch_year'); // Year of the batch
            $table->timestamps(); // For created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
