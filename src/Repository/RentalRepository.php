<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Repository;

use App\Entity\Rental;
use App\Entity\User;
use App\Enum\RentalStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry rejestr menedzerow Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }

    /**
     * Build a query for all rentals, newest first, ready to be paginated/sorted.
     *
     * Zasob i uzytkownik sa doleczani przez LEFT JOIN + addSelect, zeby
     * uniknac problemu N+1 (osobnego zapytania dla kazdego wiersza listy
     * z osobna - tytul zasobu i porownanie wlasciciela sa uzywane w
     * szablonie dla kazdego wypozyczenia).
     *
     * @return QueryBuilder zapytanie z posortowanymi wszystkimi wypozyczeniami
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->addSelect('res', 'u')
            ->leftJoin('r.resource', 'res')
            ->leftJoin('r.user', 'u')
            ->orderBy('r.rentedAt', 'DESC');
    }

    /**
     * Build a query for rentals belonging to a given user, newest first.
     *
     * @param User $user uzytkownik, ktorego wypozyczenia szukamy
     *
     * @return QueryBuilder zapytanie z wypozyczeniami danego uzytkownika
     */
    public function queryForUser(User $user): QueryBuilder
    {
        return $this->queryAll()
            ->andWhere('r.user = :user')
            ->setParameter('user', $user);
    }

    /**
     * Count rentals that are currently approved (lent out and not yet returned).
     *
     * @return int liczba aktualnie trwajacych wypozyczen
     */
    public function countActive(): int
    {
        return $this->count(['status' => RentalStatus::APPROVED]);
    }

    /**
     * Find the id and rental count of the most frequently rented resource.
     *
     * @return array{resourceId: int, rentalCount: int}|null dane najpopularniejszego zasobu albo null, jesli brak wypozyczen
     */
    public function findMostPopularResourceData(): ?array
    {
        return $this->createQueryBuilder('r')
            ->select('res.id as resourceId', 'COUNT(r.id) as rentalCount')
            ->join('r.resource', 'res')
            ->groupBy('res.id')
            ->orderBy('rentalCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
