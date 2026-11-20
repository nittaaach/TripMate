<?php

namespace App\Models;

use App\Enums\LuggageLevel;
use App\Enums\TripType;
use App\Enums\VacationPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VacationPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'origin_address',
        'origin_latitude',
        'origin_longitude',
        'start_date',
        'end_date',
        'passenger_count',
        'luggage_level',
        'trip_type',
        'estimated_distance_km',
        'estimated_duration_minutes',
        'distance_source',
        'budget_per_day',
        'needs_driver',
        'special_requirements',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'origin_latitude' => 'decimal:7',
            'origin_longitude' => 'decimal:7',
            'start_date' => 'date',
            'end_date' => 'date',
            'passenger_count' => 'integer',
            'luggage_level' => LuggageLevel::class,
            'trip_type' => TripType::class,
            'estimated_distance_km' => 'decimal:2',
            'estimated_duration_minutes' => 'integer',
            'budget_per_day' => 'decimal:2',
            'needs_driver' => 'boolean',
            'status' => VacationPlanStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(VehicleRecommendation::class)->orderBy('rank_position');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getRentalDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 1;
        }
        $days = (int) $this->start_date->diffInDays($this->end_date);
        return max(1, $days);
    }
}
