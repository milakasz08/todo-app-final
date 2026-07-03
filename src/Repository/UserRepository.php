<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry rejestr menedzerow Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Build a query for all users, ready to be paginated/sorted.
     *
     * @return QueryBuilder zapytanie ze wszystkimi uzytkownikami
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');
    }

    /**
     * Upgrade the hashed password of a user.
     *
     * @param PasswordAuthenticatedUserInterface $user              uzytkownik, ktorego haslo jest aktualizowane
     * @param string                             $newHashedPassword nowy zahashowany haslo
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
