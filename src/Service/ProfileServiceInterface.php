<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface ProfileServiceInterface.
 */
interface ProfileServiceInterface
{
    /**
     * Update the user's own profile.
     *
     * @param User        $user          uzytkownik aktualizujacy swoje dane (email juz ustawiony przez formularz)
     * @param string|null $plainPassword nowe haslo w postaci jawnej, albo null/pusty ciag, jesli haslo ma pozostac bez zmian
     */
    public function updateProfile(User $user, ?string $plainPassword): void;
}
