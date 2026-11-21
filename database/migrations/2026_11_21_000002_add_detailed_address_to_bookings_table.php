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
        Schema::table('bookings', function (Blueprint $table) {
            // Pickup administrative hierarchy & coordinates
            $table->string('pickup_province_id', 10)->nullable()->after('pickup_address');
            $table->string('pickup_regency_id', 10)->nullable()->after('pickup_province_id');
            $table->string('pickup_district_id', 10)->nullable()->after('pickup_regency_id');
            $table->string('pickup_village_id', 10)->nullable()->after('pickup_district_id');
            $table->string('pickup_rt_rw', 30)->nullable()->after('pickup_village_id');
            $table->string('pickup_postal_code', 10)->nullable()->after('pickup_rt_rw');
            $table->decimal('pickup_latitude', 10, 7)->nullable()->after('pickup_postal_code');
            $table->decimal('pickup_longitude', 10, 7)->nullable()->after('pickup_latitude');

            // Return administrative hierarchy & coordinates
            $table->string('return_province_id', 10)->nullable()->after('return_address');
            $table->string('return_regency_id', 10)->nullable()->after('return_province_id');
            $table->string('return_district_id', 10)->nullable()->after('return_regency_id');
            $table->string('return_village_id', 10)->nullable()->after('return_district_id');
            $table->string('return_rt_rw', 30)->nullable()->after('return_village_id');
            $table->string('return_postal_code', 10)->nullable()->after('return_rt_rw');
            $table->decimal('return_latitude', 10, 7)->nullable()->after('return_postal_code');
            $table->decimal('return_longitude', 10, 7)->nullable()->after('return_latitude');

            // Foreign keys
            $table->foreign('pickup_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('pickup_regency_id')->references('id')->on('regencies')->nullOnDelete();
            $table->foreign('pickup_district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('pickup_village_id')->references('id')->on('villages')->nullOnDelete();

            $table->foreign('return_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('return_regency_id')->references('id')->on('regencies')->nullOnDelete();
            $table->foreign('return_district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('return_village_id')->references('id')->on('villages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['pickup_province_id']);
            $table->dropForeign(['pickup_regency_id']);
            $table->dropForeign(['pickup_district_id']);
            $table->dropForeign(['pickup_village_id']);

            $table->dropForeign(['return_province_id']);
            $table->dropForeign(['return_regency_id']);
            $table->dropForeign(['return_district_id']);
            $table->dropForeign(['return_village_id']);

            $table->dropColumn([
                'pickup_province_id',
                'pickup_regency_id',
                'pickup_district_id',
                'pickup_village_id',
                'pickup_rt_rw',
                'pickup_postal_code',
                'pickup_latitude',
                'pickup_longitude',
                'return_province_id',
                'return_regency_id',
                'return_district_id',
                'return_village_id',
                'return_rt_rw',
                'return_postal_code',
                'return_latitude',
                'return_longitude',
            ]);
        });
    }
};
