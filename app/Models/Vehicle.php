<?php

namespace App\Models;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_code',
        'brand',
        'model',
        'year',
        'category',
        'transmission',
        'fuel_type',
        'fuel_efficiency_km_per_l',
        'battery_range_km',
        'seats',
        'luggage_capacity_l',
        'ground_clearance_mm',
        'suitable_terrain',
        'suitable_road_conditions',
        'daily_rate',
        'deposit_amount',
        'requires_driver',
        'has_ac',
        'is_available',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'category' => VehicleCategory::class,
            'transmission' => TransmissionType::class,
            'fuel_type' => FuelType::class,
            'fuel_efficiency_km_per_l' => 'decimal:2',
            'battery_range_km' => 'decimal:2',
            'seats' => 'integer',
            'luggage_capacity_l' => 'integer',
            'ground_clearance_mm' => 'integer',
            'suitable_terrain' => 'array',
            'suitable_road_conditions' => 'array',
            'daily_rate' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'requires_driver' => 'boolean',
            'has_ac' => 'boolean',
            'is_available' => 'boolean',
            'status' => VehicleStatus::class,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', VehicleStatus::Active)
                     ->where('is_available', true);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(VehicleRecommendation::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->avg('rating') ?? 0);
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->primaryImage ?? $this->images->first();
        if ($primary && $primary->image_path) {
            return asset('storage/' . $primary->image_path);
        }
        return asset('images/default-vehicle.png');
    }
}
