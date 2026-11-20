<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Aktif',
            self::Maintenance => 'Perawatan',
            self::Inactive => 'Nonaktif',
        };
    }
}
