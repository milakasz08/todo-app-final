<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class ProfileService.
 *
 * Pozwala zalogowanemu użytkownikowi zaktualizować własny e-mail i hasło.
 */
class ProfileService implements ProfileServiceInterface
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
     * Aktualizuje dane użytkownika. Hasło jest zmieniane tylko wtedy, gdy
     * $plainPassword nie jest puste - dzięki temu można zapisać sam e-mail.
     *
     * @param User        $user          uzytkownik aktualizujacy swoje dane (email juz ustawiony przez formularz)
     * @param string|null $plainPassword nowe haslo w postaci jawnej, albo null/pusty ciag, jesli haslo ma pozostac bez zmian
     */
    public function updateProfile(User $user, ?string $plainPassword): void
    {
        if (null !== $plainPassword && '' !== $plainPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }

        $this->entityManager->flush();
    }
}
