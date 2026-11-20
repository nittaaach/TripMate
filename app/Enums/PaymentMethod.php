<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case BankTransfer = 'bank_transfer';
    case EWallet = 'e_wallet';
    case Card = 'card';
    case Cash = 'cash';
    case Gateway = 'gateway';

    public function label(): string
    {
        return match($this) {
            self::BankTransfer => 'Transfer Bank Manual',
            self::EWallet => 'E-Wallet',
            self::Card => 'Kartu Kredit / Debit',
            self::Cash => 'Tunai di Tempat',
            self::Gateway => 'Payment Gateway',
        };
    }
}
