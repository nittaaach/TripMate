<?php

namespace App\Services\Availability;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class VehicleAvailabilityService
{
    /**
     * Check if a vehicle is available for the given date/time range.
     */
    public function isAvailable(
        int|Vehicle $vehicle,
        CarbonInterface|string $pickupDatetime,
        CarbonInterface|string $returnDatetime,
        ?int $excludeBookingId = null,
    ): bool {
        $vehicleModel = $vehicle instanceof Vehicle ? $vehicle : Vehicle::find($vehicle);

        if (!$vehicleModel || $vehicleModel->status !== VehicleStatus::Active || !$vehicleModel->is_available) {
            return false;
        }

        $start = Carbon::parse($pickupDatetime);
        $end = Carbon::parse($returnDatetime);

        if ($end->lessThanOrEqualTo($start)) {
            return false;
        }

        // 1. Check conflicting bookings
        $hasBookingConflict = Booking::query()
            ->where('vehicle_id', $vehicleModel->id)
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where(function ($query) {
                $query->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Ongoing])
                      ->orWhere(function ($q) {
                          // Pending holds unit for max 24 hours
                          $q->where('status', BookingStatus::Pending)
                            ->where('created_at', '>=', now()->subHours(24));
                      });
            })
            ->where(function ($query) use ($start, $end) {
                $query->where('pickup_datetime', '<', $end)
                      ->where('return_datetime', '>', $start);
            })
            ->exists();

        if ($hasBookingConflict) {
            return false;
        }

        // 2. Check conflicting maintenance
        $hasMaintenanceConflict = VehicleMaintenance::query()
            ->where('vehicle_id', $vehicleModel->id)
            ->whereIn('status', [MaintenanceStatus::Scheduled, MaintenanceStatus::InProgress])
            ->where(function ($query) use ($start, $end) {
                $query->where('start_datetime', '<', $end)
                      ->where(function ($sub) use ($start) {
                          $sub->whereNull('end_datetime')
                              ->orWhere('end_datetime', '>', $start);
                      });
            })
            ->exists();

        return !$hasMaintenanceConflict;
    }

    /**
     * Filter a collection of vehicles, returning only those available in the date range.
     *
     * @param  \Illuminate\Support\Collection<int, Vehicle>  $vehicles
     * @return \Illuminate\Support\Collection<int, Vehicle>
     */
    public function removeConflicts(
        $vehicles,
        CarbonInterface|string $pickupDatetime,
        CarbonInterface|string $returnDatetime,
        ?int $excludeBookingId = null,
    ) {
        $start = Carbon::parse($pickupDatetime);
        $end = Carbon::parse($returnDatetime);

        $vehicleIds = $vehicles->pluck('id')->all();

        // Find booked vehicle IDs
        $bookedVehicleIds = Booking::query()
            ->whereIn('vehicle_id', $vehicleIds)
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where(function ($query) {
                $query->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Ongoing])
                      ->orWhere(function ($q) {
                          $q->where('status', BookingStatus::Pending)
                            ->where('created_at', '>=', now()->subHours(24));
                      });
            })
            ->where(function ($query) use ($start, $end) {
                $query->where('pickup_datetime', '<', $end)
                      ->where('return_datetime', '>', $start);
            })
            ->pluck('vehicle_id')
            ->all();

        // Find maintenance vehicle IDs
        $maintenanceVehicleIds = VehicleMaintenance::query()
            ->whereIn('vehicle_id', $vehicleIds)
            ->whereIn('status', [MaintenanceStatus::Scheduled, MaintenanceStatus::InProgress])
            ->where(function ($query) use ($start, $end) {
                $query->where('start_datetime', '<', $end)
                      ->where(function ($sub) use ($start) {
                          $sub->whereNull('end_datetime')
                              ->orWhere('end_datetime', '>', $start);
                      });
            })
            ->pluck('vehicle_id')
            ->all();

        $unavailableIds = array_unique(array_merge($bookedVehicleIds, $maintenanceVehicleIds));

        return $vehicles->reject(fn (Vehicle $v) => in_array($v->id, $unavailableIds, true));
    }
}
