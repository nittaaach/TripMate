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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('vacation_plan_id')->nullable()->constrained('vacation_plans')->nullOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('restrict');
            $table->dateTime('pickup_datetime')->index();
            $table->dateTime('return_datetime')->index();
            $table->text('pickup_address');
            $table->text('return_address');
            $table->decimal('rental_days', 5, 2)->default(1);
            $table->decimal('daily_rate_snapshot', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('additional_fee', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('status', 20)->default('pending')->index(); // pending, confirmed, ongoing, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
