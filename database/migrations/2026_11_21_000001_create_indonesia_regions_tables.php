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
        Schema::create('provinces', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('province_id', 10);
            $table->string('name', 100);
            $table->string('type', 20)->default('Kabupaten'); // Kabupaten / Kota
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('regency_id', 10);
            $table->string('name', 100);
            $table->timestamps();

            $table->foreign('regency_id')->references('id')->on('regencies')->onDelete('cascade');
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('district_id', 10);
            $table->string('name', 100);
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};
