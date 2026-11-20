<?php

namespace App\Enums;

enum VacationPlanStatus: string
{
    case Draft = 'draft';
    case Recommended = 'recommended';
    case Booked = 'booked';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draf',
            self::Recommended => 'Direkomendasikan',
            self::Booked => 'Dipesan',
            self::Cancelled => 'Dibatalkan',
            self::Completed => 'Selesai',
        };
    }
}
