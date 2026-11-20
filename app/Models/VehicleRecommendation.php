<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacation_plan_id',
        'vehicle_id',
        'rank_position',
        'match_score',
        'distance_fit_score',
        'capacity_fit_score',
        'terrain_fit_score',
        'budget_fit_score',
        'recommendation_reason',
        'is_selected',
        'algorithm_version',
    ];

    protected function casts(): array
    {
        return [
            'rank_position' => 'integer',
            'match_score' => 'decimal:2',
            'distance_fit_score' => 'decimal:2',
            'capacity_fit_score' => 'decimal:2',
            'terrain_fit_score' => 'decimal:2',
            'budget_fit_score' => 'decimal:2',
            'is_selected' => 'boolean',
        ];
    }

    public function vacationPlan(): BelongsTo
    {
        return $this->belongsTo(VacationPlan::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
