<?php

namespace App\Services\Routing\Providers;

use App\Services\Routing\RouteResult;
use App\Services\Routing\RoutingServiceInterface;

class NullRoutingProvider implements RoutingServiceInterface
{
    /**
     * Fallback stub provider when external API key is not configured.
     * Uses Haversine distance if coordinates are present, or returns graceful success.
     */
    public function estimate(
        string $originAddress,
        ?float $originLat,
        ?float $originLng,
        ?float $destLat,
        ?float $destLng,
    ): RouteResult {
        if ($originLat && $originLng && $destLat && $destLng) {
            // Calculate approximate road distance (Haversine * 1.3 road winding factor)
            $earthRadiusKm = 6371;
            $dLat = deg2rad($destLat - $originLat);
            $dLon = deg2rad($destLng - $originLng);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($originLat)) * cos(deg2rad($destLat)) *
                sin($dLon / 2) * sin($dLon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $straightDistance = $earthRadiusKm * $c;
            $roadDistance = round($straightDistance * 1.25, 2);

            // Average speed assumption 45 km/h
            $durationMinutes = max(15, (int) round(($roadDistance / 45) * 60));

            return RouteResult::successful(
                distanceKm: $roadDistance,
                durationMinutes: $durationMinutes,
                source: 'Coordinate Estimation (Fallback)',
            );
        }

        // Without coordinates, return graceful stub
        return RouteResult::failed('Koordinat belum tersedia untuk estimasi otomatis');
    }
}
