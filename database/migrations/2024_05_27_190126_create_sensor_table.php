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
        Schema::create('data_sensor', function (Blueprint $table) {
            $table->id('id');
            $table->string('id_device');
            $table->float('suhu');
            $table->float('kelembapan_udara');
            $table->float('kelembapan_tanah');
            $table->float('ph_tanah');
            $table->float('npk');
            $table->timestamp('timestamp_pengukuran')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_sensor');
    }
};