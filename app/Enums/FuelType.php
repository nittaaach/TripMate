<?php

namespace App\Enums;

enum FuelType: string
{
    case Petrol = 'petrol';
    case Diesel = 'diesel';
    case Hybrid = 'hybrid';
    case Electric = 'electric';

    public function label(): string
    {
        return match($this) {
            self::Petrol => 'Bensin (Petrol)',
            self::Diesel => 'Solar (Diesel)',
            self::Hybrid => 'Hybrid',
            self::Electric => 'Listrik (Electric)',
        };
    }
}
