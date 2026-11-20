<?php

namespace App\Services\Booking;

use App\Models\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class BookingPricingService
{
    /**
     * Calculate price breakdown for a rental booking.
     *
     * @return array{
     *     rental_days: int,
     *     daily_rate_snapshot: float,
     *     subtotal: float,
     *     additional_fee: float,
     *     discount_amount: float,
     *     deposit_amount: float,
     *     total_amount: float
     * }
     */
    public function calculate(
        Vehicle $vehicle,
        CarbonInterface|string $pickupDatetime,
        CarbonInterface|string $returnDatetime,
        bool $needsDriver = false,
        float $discountAmount = 0.0,
    ): array {
        $pickup = Carbon::parse($pickupDatetime);
        $return = Carbon::parse($returnDatetime);

        // Decision Q1: Calendar day difference, minimum 1 day
        $rentalDays = max(1, (int) $pickup->diffInDays($return));

        $dailyRate = (float) $vehicle->daily_rate;
        $subtotal = $dailyRate * $rentalDays;

        // Additional fee: driver fee Rp 150.000 / day if requested
        $driverDailyFee = 150000.0;
        $additionalFee = $needsDriver ? ($driverDailyFee * $rentalDays) : 0.0;

        $deposit = (float) $vehicle->deposit_amount;
        $total = max(0, ($subtotal + $additionalFee) - $discountAmount);

        return [
            'rental_days' => $rentalDays,
            'daily_rate_snapshot' => $dailyRate,
            'subtotal' => $subtotal,
            'additional_fee' => $additionalFee,
            'discount_amount' => $discountAmount,
            'deposit_amount' => $deposit,
            'total_amount' => $total,
        ];
    }
}
