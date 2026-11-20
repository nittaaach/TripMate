<?php

namespace App\Enums;

enum VehicleCategory: string
{
    case CityCar = 'city_car';
    case Sedan = 'sedan';
    case Mpv = 'mpv';
    case Suv = 'suv';
    case Jeep = 'jeep';
    case Van = 'van';
    case Pickup = 'pickup';

    public function label(): string
    {
        return match($this) {
            self::CityCar => 'City Car',
            self::Sedan => 'Sedan',
            self::Mpv => 'MPV',
            self::Suv => 'SUV',
            self::Jeep => 'Jeep',
            self::Van => 'Van',
            self::Pickup => 'Pickup',
        };
    }
}
