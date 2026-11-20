<?php

namespace App\Services\Routing;

class RouteResult
{
    public function __construct(
        public bool $success,
        public ?float $distanceKm = null,
        public ?int $durationMinutes = null,
        public ?string $source = null,
        public ?string $errorMessage = null,
    ) {}

    public static function successful(float $distanceKm, int $durationMinutes, string $source = 'internal'): self
    {
        return new self(
            success: true,
            distanceKm: $distanceKm,
            durationMinutes: $durationMinutes,
            source: $source,
        );
    }

    public static function failed(?string $errorMessage = null): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
        );
    }
}
