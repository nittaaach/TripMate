<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'provider_reference',
        'amount',
        'currency',
        'status',
        'paid_at',
        'proof_path',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getProofUrlAttribute(): ?string
    {
        if ($this->proof_path) {
            return asset('storage/' . $this->proof_path);
        }
        return null;
    }
}
