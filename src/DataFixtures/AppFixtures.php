<?php

/*
 * This file is part of the EPI project.
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load the fixtures.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // 1. Tworzenie administratora
        $admin = new User();
        $admin->setEmail('admin1@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'haslo123'));
        $admin->setIsVerified(true);
        $manager->persist($admin);

        // 2. Tworzenie zwykłego użytkownika
        $user = new User();
        $user->setEmail('user1@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'haslo1234'));
        $user->setIsVerified(true);
        $manager->persist($user);

        $manager->flush();
    }
}
