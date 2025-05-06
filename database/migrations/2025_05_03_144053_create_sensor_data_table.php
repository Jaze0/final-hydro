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
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 5, 2);     // more precise than float
            $table->decimal('humidity', 5, 2);        // more precise than float
            $table->decimal('soil_moisture', 5, 2);   // better control for sensor data
            $table->decimal('water_level', 5, 2);     // added water level column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
