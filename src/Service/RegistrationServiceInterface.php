<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface RegistrationServiceInterface.
 */
interface RegistrationServiceInterface
{
    /**
     * Register a new user with the default ROLE_USER role.
     *
     * @param User   $user          nowo tworzony uzytkownik (email juz ustawiony przez formularz)
     * @param string $plainPassword haslo w postaci jawnej
     */
    public function register(User $user, string $plainPassword): void;
}
