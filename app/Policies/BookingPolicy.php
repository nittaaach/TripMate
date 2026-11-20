<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role !== UserRole::Customer;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($booking->status->value === 'completed' || $booking->status->value === 'cancelled') {
            return false;
        }

        return $user->id === $booking->user_id || $user->role !== UserRole::Customer;
    }

    public function updateStatus(User $user, Booking $booking): bool
    {
        return $user->role !== UserRole::Customer;
    }
}
