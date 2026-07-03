<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Enum;

/**
 * Enum RentalStatus.
 *
 * Reprezentuje status wniosku o wypożyczenie.
 */
enum RentalStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case RETURNED = 'RETURNED';

    /**
     * Zwraca klucz tłumaczenia używany do wyświetlenia etykiety statusu w Twig.
     *
     * @return string klucz tlumaczenia
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'rental.status.pending',
            self::APPROVED => 'rental.status.approved',
            self::REJECTED => 'rental.status.rejected',
            self::RETURNED => 'rental.status.returned',
        };
    }
}
