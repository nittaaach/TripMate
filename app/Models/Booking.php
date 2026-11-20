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
        'return_address',
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
}
