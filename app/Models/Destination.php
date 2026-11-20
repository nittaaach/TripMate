<?php

namespace App\Models;

use App\Enums\RoadCondition;
use App\Enums\TerrainType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'province',
        'latitude',
        'longitude',
        'terrain_type',
        'road_condition',
        'accessibility_notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'terrain_type' => TerrainType::class,
            'road_condition' => RoadCondition::class,
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function vacationPlans(): HasMany
    {
        return $this->hasMany(VacationPlan::class);
    }
}
