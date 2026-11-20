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
        Schema::create('vehicle_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacation_plan_id')->constrained('vacation_plans')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->unsignedTinyInteger('rank_position')->default(1);
            $table->decimal('match_score', 5, 2)->default(0);
            $table->decimal('distance_fit_score', 5, 2)->nullable();
            $table->decimal('capacity_fit_score', 5, 2)->nullable();
            $table->decimal('terrain_fit_score', 5, 2)->nullable();
            $table->decimal('budget_fit_score', 5, 2)->nullable();
            $table->text('recommendation_reason');
            $table->boolean('is_selected')->default(false);
            $table->string('algorithm_version', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_recommendations');
    }
};
