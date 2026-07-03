<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Repository;

use App\Entity\Resource;
use App\Enum\MediaType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<resource>
 */
class ResourceRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry rejestr menedzerow Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    /**
     * Build a query for resources filtered by media type and/or tags,
     * ordered from the newest. Returns a QueryBuilder (not the results)
     * so it can be paginated by KnpPaginator.
     *
     * @param MediaType|null $type   typ zasobu (stala, a nie tekst z bazy)
     * @param int[]          $tagIds identyfikatory tagow do filtrowania
     *
     * @return QueryBuilder zapytanie z zasobami spelniajacymi kryteria
     */
    public function queryFiltered(?MediaType $type, array $tagIds): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC');

        if ([] !== $tagIds) {
            $qb->join('r.tags', 't')
                ->andWhere('t.id IN (:tagIds)')
                ->setParameter('tagIds', $tagIds)
                ->groupBy('r.id'); // żeby zasób z wieloma dopasowanymi tagami nie powtarzał się
        }

        if (null !== $type) {
            $qb->andWhere('r.type = :type')
                ->setParameter('type', $type);
        }

        return $qb;
    }
}
