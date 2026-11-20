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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_code', 30)->unique();
            $table->string('brand', 80);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('category', 30)->default('mpv')->index(); // city_car, sedan, mpv, suv, jeep, van, pickup
            $table->string('transmission', 20)->default('automatic'); // automatic, manual
            $table->string('fuel_type', 20)->default('petrol'); // petrol, diesel, hybrid, electric
            $table->decimal('fuel_efficiency_km_per_l', 6, 2)->nullable();
            $table->decimal('battery_range_km', 7, 2)->nullable();
            $table->unsignedTinyInteger('seats')->default(5);
            $table->unsignedSmallInteger('luggage_capacity_l')->nullable();
            $table->unsignedSmallInteger('ground_clearance_mm')->nullable();
            $table->json('suitable_terrain'); // e.g. ["city", "highway"]
            $table->json('suitable_road_conditions'); // e.g. ["good", "moderate"]
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->boolean('requires_driver')->default(false);
            $table->boolean('has_ac')->default(true);
            $table->boolean('is_available')->default(true)->index();
            $table->string('status', 20)->default('active')->index(); // active, maintenance, inactive
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
