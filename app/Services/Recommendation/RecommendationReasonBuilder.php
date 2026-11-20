<?php

namespace App\Services\Recommendation;

use App\Models\VacationPlan;
use App\Models\Vehicle;

class RecommendationReasonBuilder
{
    /**
     * Build natural, transparent reasons why this vehicle is recommended.
     *
     * @param  array<string, float>  $scores
     * @return string
     */
    public function build(Vehicle $vehicle, VacationPlan $plan, array $scores): string
    {
        $reasons = [];

        // 1. Capacity reason
        $passengers = $plan->passenger_count;
        $effectiveSeats = $plan->needs_driver ? max(1, $vehicle->seats - 1) : $vehicle->seats;
        $driverNote = $plan->needs_driver ? ' (termasuk sopir)' : '';
        $reasons[] = "Kapasitas {$vehicle->seats} kursi{$driverNote} sangat pas untuk {$passengers} penumpang beserta bagasi {$plan->luggage_level?->label()}.";

        // 2. Terrain & Road reason
        $destName = $plan->destination?->name ?? 'destinasi';
        $destTerrainLabel = $plan->destination?->terrain_type?->label() ?? 'medan perjalanan';
        $destRoadLabel = $plan->destination?->road_condition?->label() ?? 'kondisi jalan';

        if ($vehicle->ground_clearance_mm && $vehicle->ground_clearance_mm >= 190) {
            $reasons[] = "Ground clearance {$vehicle->ground_clearance_mm} mm tangguh untuk {$destTerrainLabel} dan kondisi jalan {$destRoadLabel} di {$destName}.";
        } else {
            $reasons[] = "Sangat sesuai untuk karakteristik {$destTerrainLabel} dengan jalan {$destRoadLabel}.";
        }

        // 3. Distance & Efficiency reason
        if ($plan->estimated_distance_km) {
            $distance = number_format((float) $plan->estimated_distance_km, 1);
            if ($vehicle->fuel_type?->value === 'diesel') {
                $reasons[] = "Torsi mesin diesel bertenaga dan efisien untuk perjalanan jarak {$distance} km.";
            } elseif ($vehicle->fuel_type?->value === 'hybrid') {
                $reasons[] = "Teknologi hybrid hemat bahan bakar untuk rute {$distance} km.";
            } elseif ($vehicle->fuel_type?->value === 'electric') {
                $range = $vehicle->battery_range_km ? " hingga {$vehicle->battery_range_km} km" : "";
                $reasons[] = "Kendaraan listrik ramah lingkungan dengan daya jangkau{$range}.";
            } elseif ($vehicle->fuel_efficiency_km_per_l) {
                $reasons[] = "Estimasi rute {$distance} km terjangkau dengan efisiensi {$vehicle->fuel_efficiency_km_per_l} km/liter.";
            } else {
                $reasons[] = "Nyaman untuk estimasi perjalanan {$distance} km.";
            }
        }

        // 4. Budget reason
        if ($plan->budget_per_day && $plan->budget_per_day > 0) {
            $rate = (float) $vehicle->daily_rate;
            $budget = (float) $plan->budget_per_day;
            if ($rate <= $budget) {
                $reasons[] = "Tarif Rp " . number_format($rate, 0, ',', '.') . "/hari hemat dan berada di bawah batas anggaran Anda (Rp " . number_format($budget, 0, ',', '.') . ").";
            } else {
                $reasons[] = "Tarif harian Rp " . number_format($rate, 0, ',', '.') . "/hari mendekati target anggaran Anda.";
            }
        } else {
            $reasons[] = "Tarif sewa kompetitif: Rp " . number_format((float) $vehicle->daily_rate, 0, ',', '.') . "/hari.";
        }

        return implode(' ', $reasons);
    }
}
