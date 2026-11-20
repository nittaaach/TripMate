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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('payment_method', 30)->default('bank_transfer'); // bank_transfer, e_wallet, card, cash, gateway
            $table->string('provider_reference', 120)->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('IDR');
            $table->string('status', 20)->default('pending')->index(); // pending, paid, failed, refunded
            $table->dateTime('paid_at')->nullable();
            $table->string('proof_path', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
