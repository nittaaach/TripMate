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
        Schema::create('vacation_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('restrict');
            $table->text('origin_address');
            $table->decimal('origin_latitude', 10, 7)->nullable();
            $table->decimal('origin_longitude', 10, 7)->nullable();
            $table->date('start_date')->index();
            $table->date('end_date');
            $table->unsignedTinyInteger('passenger_count')->default(1);
            $table->string('luggage_level', 20)->default('medium'); // light, medium, heavy
            $table->string('trip_type', 20)->default('round_trip'); // one_way, round_trip, multi_stop
            $table->decimal('estimated_distance_km', 8, 2)->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->string('distance_source', 80)->nullable();
            $table->decimal('budget_per_day', 12, 2)->nullable();
            $table->boolean('needs_driver')->default(false);
            $table->text('special_requirements')->nullable();
            $table->string('status', 20)->default('draft')->index(); // draft, recommended, booked, cancelled, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_plans');
    }
};
