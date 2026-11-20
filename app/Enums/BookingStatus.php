<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Confirmed => 'Terkonfirmasi',
            self::Ongoing => 'Sedang Berjalan',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Determines if this status blocks vehicle availability.
     */
    public function blocksVehicle(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed, self::Ongoing]);
    }
}
