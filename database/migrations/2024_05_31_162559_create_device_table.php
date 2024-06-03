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
        Schema::create('device', function (Blueprint $table) {
            $table->string('id');
            $table->foreignId('tipe_intruksi')->nullable();
            $table->foreignId('fertilization_controller')->nullable();
            $table->foreignId('irrigation_controller')->nullable();
            $table->float('durasi')->nullable();
            $table->boolean('isActive')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device');
    }
};
