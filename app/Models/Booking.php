<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'vacation_plan_id',
        'vehicle_id',
        'pickup_datetime',
        'return_datetime',
        'pickup_address',
        'pickup_province_id',
        'pickup_regency_id',
        'pickup_district_id',
        'pickup_village_id',
        'pickup_rt_rw',
        'pickup_postal_code',
        'pickup_latitude',
        'pickup_longitude',
        'return_address',
        'return_province_id',
        'return_regency_id',
        'return_district_id',
        'return_village_id',
        'return_rt_rw',
        'return_postal_code',
        'return_latitude',
        'return_longitude',
        'rental_days',
        'daily_rate_snapshot',
        'subtotal',
        'discount_amount',
        'additional_fee',
        'deposit_amount',
        'total_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_datetime' => 'datetime',
            'return_datetime' => 'datetime',
            'pickup_latitude' => 'decimal:7',
            'pickup_longitude' => 'decimal:7',
            'return_latitude' => 'decimal:7',
            'return_longitude' => 'decimal:7',
            'rental_days' => 'decimal:2',
            'daily_rate_snapshot' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'additional_fee' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => BookingStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacationPlan(): BelongsTo
    {
        return $this->belongsTo(VacationPlan::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    // Regional relations for Pickup
    public function pickupProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'pickup_province_id');
    }

    public function pickupRegency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'pickup_regency_id');
    }

    public function pickupDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'pickup_district_id');
    }

    public function pickupVillage(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'pickup_village_id');
    }

    // Regional relations for Return
    public function returnProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'return_province_id');
    }

    public function returnRegency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'return_regency_id');
    }

    public function returnDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'return_district_id');
    }

    public function returnVillage(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'return_village_id');
    }

    // Accessors for full formatted addresses
    public function getFormattedPickupAddressAttribute(): string
    {
        $parts = array_filter([
            $this->pickup_address,
            $this->pickup_rt_rw,
            $this->pickupVillage ? 'Kel. ' . $this->pickupVillage->name : null,
            $this->pickupDistrict ? 'Kec. ' . $this->pickupDistrict->name : null,
            $this->pickupRegency ? $this->pickupRegency->name : null,
            $this->pickupProvince ? $this->pickupProvince->name : null,
            $this->pickup_postal_code,
        ]);

        return !empty($parts) ? implode(', ', $parts) : ($this->pickup_address ?? '-');
    }

    public function getFormattedReturnAddressAttribute(): string
    {
        $parts = array_filter([
            $this->return_address,
            $this->return_rt_rw,
            $this->returnVillage ? 'Kel. ' . $this->returnVillage->name : null,
            $this->returnDistrict ? 'Kec. ' . $this->returnDistrict->name : null,
            $this->returnRegency ? $this->returnRegency->name : null,
            $this->returnProvince ? $this->returnProvince->name : null,
            $this->return_postal_code,
        ]);

        return !empty($parts) ? implode(', ', $parts) : ($this->return_address ?? '-');
    }

    public function getHasPickupCoordinatesAttribute(): bool
    {
        return !is_null($this->pickup_latitude) && !is_null($this->pickup_longitude);
    }

    public function getHasReturnCoordinatesAttribute(): bool
    {
        return !is_null($this->return_latitude) && !is_null($this->return_longitude);
    }

    public function getPickupMapsUrlAttribute(): ?string
    {
        if (!$this->has_pickup_coordinates) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->pickup_latitude},{$this->pickup_longitude}";
    }

    public function getReturnMapsUrlAttribute(): ?string
    {
        if (!$this->has_return_coordinates) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->return_latitude},{$this->return_longitude}";
    }
}
