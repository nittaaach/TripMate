<?php

namespace App\Enums;

enum LuggageLevel: string
{
    case Light = 'light';
    case Medium = 'medium';
    case Heavy = 'heavy';

    public function label(): string
    {
        return match($this) {
            self::Light => 'Ringan (1-2 tas kecil)',
            self::Medium => 'Sedang (2-3 koper sedang)',
            self::Heavy => 'Banyak (3+ koper besar)',
        };
    }
}
