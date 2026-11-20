<?php

namespace App\Services\Routing;

interface RoutingServiceInterface
{
    /**
     * Estimate driving distance and duration from origin to destination.
     *
     * @param  string  $originAddress
     * @param  float|null  $originLat
     * @param  float|null  $originLng
     * @param  float|null  $destLat
     * @param  float|null  $destLng
     * @return RouteResult
     */
    public function estimate(
        string $originAddress,
        ?float $originLat,
        ?float $originLng,
        ?float $destLat,
        ?float $destLng,
    ): RouteResult;
}
