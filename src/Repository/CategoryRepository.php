<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry rejestr menedzerow Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Build a query for all categories, ready to be paginated/sorted.
     *
     * @return QueryBuilder zapytanie ze wszystkimi kategoriami
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC');
    }
}
