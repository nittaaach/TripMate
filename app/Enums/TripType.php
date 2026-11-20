<?php

namespace App\Enums;

enum TripType: string
{
    case OneWay = 'one_way';
    case RoundTrip = 'round_trip';
    case MultiStop = 'multi_stop';

    public function label(): string
    {
        return match($this) {
            self::OneWay => 'Satu Arah',
            self::RoundTrip => 'Pulang Pergi',
            self::MultiStop => 'Banyak Titik (Multi-Stop)',
        };
    }
}
