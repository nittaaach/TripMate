<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Paid => 'Lunas / Terverifikasi',
            self::Failed => 'Gagal / Ditolak',
            self::Refunded => 'Dikembalikan (Refund)',
        };
    }
}
