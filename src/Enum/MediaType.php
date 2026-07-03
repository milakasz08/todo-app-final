<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Enum;

/**
 * Enum MediaType.
 *
 * Reprezentuje typ zasobu w systemie. Wartości przechowywane w bazie danych
 * są stałymi, niezależnymi od języka identyfikatorami (np. "film"), dzięki
 * czemu zmiana języka interfejsu nie wpływa na dane zapisane w bazie -
 * do wyświetlenia etykiety służy metoda label(), która korzysta z tłumaczeń.
 */
enum MediaType: string
{
    case BOOK = 'ksiazka';
    case FILM = 'film';
    case RECORD = 'plyta';

    /**
     * Get the translation key for the media type label.
     *
     * @return string klucz tlumaczenia etykiety typu zasobu
     */
    public function label(): string
    {
        return match ($this) {
            self::BOOK => 'resource.type.book',
            self::FILM => 'resource.type.film',
            self::RECORD => 'resource.type.record',
        };
    }
}
