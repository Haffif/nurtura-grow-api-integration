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
        Schema::create('irrigation', function (Blueprint $table) {
            $table->id();
            $table->float('rekomendasi_volume')->nullable();
            $table->foreignId('kondisi')->nullable();
            $table->foreignId('saran')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('irrigation');
    }
};
