<?php

/*
 * This file is part of the EPI project.
 */

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Category;
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
     * @param UserPasswordHasherInterface $passwordHasher opis parametru.     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load the fixtures.
     *
     * @param ObjectManager $manager opis parametru.     *
     * @return void opis wartosci zwracanej.     */
    public function load(ObjectManager $manager): void
    {
        $tag1 = new Tag();
        $tag1->setName('Nowość');
        $manager->persist($tag1);

        $tag2 = new Tag();
        $tag2->setName('Polecane');
        $manager->persist($tag2);

        $tag3 = new Tag();
        $tag3->setName('Bestseller');
        $manager->persist($tag3);

        $tag4 = new Tag();
        $tag4->setName('Klasyk');
        $manager->persist($tag4);

        $tag5 = new Tag();
        $tag5->setName('Fantastyka');
        $manager->persist($tag5);

        $tag6 = new Tag();
        $tag6->setName('Sci-fi');
        $manager->persist($tag6);

        $tag7 = new Tag();
        $tag7->setName('Literatura obyczajowa');
        $manager->persist($tag7);

        $tag8 = new Tag();
        $tag8->setName('Kryminał/Thriller');
        $manager->persist($tag8);

        $tag9 = new Tag();
        $tag9->setName('Romantyczne');
        $manager->persist($tag9);

            $cat = new Category();
            $cat->setName('Dla dzieci');
            $manager->persist($cat);


        $cat = new Category();
        $cat->setName('Dla młodzieży');
        $manager->persist($cat);

        $cat = new Category();
        $cat->setName('Dla dorosłych');
        $manager->persist($cat);
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
