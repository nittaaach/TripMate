<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, Booking $booking): bool
    {
        // Booking must belong to user, be completed, and have no existing review
        return $booking->user_id === $user->id
            && $booking->status === BookingStatus::Completed
            && $booking->review === null;
    }
}
