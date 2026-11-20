<?php

namespace Tests\Unit;

use App\Models\Vehicle;
use App\Services\Booking\BookingPricingService;
use Carbon\Carbon;
use Tests\TestCase;

class BookingPricingServiceTest extends TestCase
{
    public function test_it_calculates_calendar_day_rental_and_subtotal(): void
    {
        $service = new BookingPricingService();
        $vehicle = new Vehicle([
            'daily_rate' => 500000,
            'deposit_amount' => 1000000,
        ]);

        $pickup = Carbon::parse('2026-11-20 09:00:00');
        $return = Carbon::parse('2026-11-23 18:00:00'); // 3 days

        $pricing = $service->calculate($vehicle, $pickup, $return, needsDriver: false);

        $this->assertEquals(3, $pricing['rental_days']);
        $this->assertEquals(500000.0, $pricing['daily_rate_snapshot']);
        $this->assertEquals(1500000.0, $pricing['subtotal']);
        $this->assertEquals(0.0, $pricing['additional_fee']);
        $this->assertEquals(1000000.0, $pricing['deposit_amount']);
        $this->assertEquals(1500000.0, $pricing['total_amount']);
    }

    public function test_it_adds_driver_fee_per_rental_day(): void
    {
        $service = new BookingPricingService();
        $vehicle = new Vehicle([
            'daily_rate' => 400000,
            'deposit_amount' => 500000,
        ]);

        $pickup = Carbon::parse('2026-11-20 08:00:00');
        $return = Carbon::parse('2026-11-22 17:00:00'); // 2 days

        $pricing = $service->calculate($vehicle, $pickup, $return, needsDriver: true);

        $this->assertEquals(2, $pricing['rental_days']);
        $this->assertEquals(800000.0, $pricing['subtotal']);
        $this->assertEquals(300000.0, $pricing['additional_fee']); // 150.000 * 2
        $this->assertEquals(1100000.0, $pricing['total_amount']);
    }

    public function test_minimum_rental_is_one_day(): void
    {
        $service = new BookingPricingService();
        $vehicle = new Vehicle([
            'daily_rate' => 350000,
            'deposit_amount' => 500000,
        ]);

        $pickup = Carbon::parse('2026-11-20 08:00:00');
        $return = Carbon::parse('2026-11-20 18:00:00'); // same day

        $pricing = $service->calculate($vehicle, $pickup, $return);

        $this->assertEquals(1, $pricing['rental_days']);
        $this->assertEquals(350000.0, $pricing['total_amount']);
    }
}
