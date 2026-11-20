<?php

namespace App\Enums;

enum RoadCondition: string
{
    case Good = 'good';
    case Moderate = 'moderate';
    case Rough = 'rough';
    case Offroad = 'offroad';

    public function label(): string
    {
        return match($this) {
            self::Good => 'Bagus / Mulus',
            self::Moderate => 'Sedang / Bergelombang',
            self::Rough => 'Kasar / Berlubang',
            self::Offroad => 'Offroad / Tanah & Batu',
        };
    }
}
