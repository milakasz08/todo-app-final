<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class RegistrationService.
 */
class RegistrationService implements RegistrationServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher hasher hasel uzytkownikow
     * @param EntityManagerInterface      $entityManager  menedzer encji Doctrine
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Haszuje podane hasło, nadaje domyślną rolę ROLE_USER i zapisuje konto.
     *
     * @param User   $user          nowo tworzony uzytkownik (email juz ustawiony przez formularz)
     * @param string $plainPassword haslo w postaci jawnej
     */
    public function register(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
