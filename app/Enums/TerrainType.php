<?php

namespace App\Enums;

enum TerrainType: string
{
    case City = 'city';
    case Highway = 'highway';
    case Mountain = 'mountain';
    case Beach = 'beach';
    case Offroad = 'offroad';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match($this) {
            self::City => 'Perkotaan',
            self::Highway => 'Jalan Bebas Hambatan (Tol)',
            self::Mountain => 'Pegunungan / Perbukitan',
            self::Beach => 'Pesisir Pantai',
            self::Offroad => 'Offroad / Bebatuan',
            self::Mixed => 'Kombinasi / Campuran',
        };
    }
}
