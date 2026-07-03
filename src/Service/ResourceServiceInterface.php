<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Resource;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ResourceServiceInterface.
 */
interface ResourceServiceInterface
{
    /**
     * @param string|null $type   identyfikator typu zasobu przekazany w zapytaniu (wartosc enuma MediaType)
     * @param int[]       $tagIds identyfikatory tagow do filtrowania
     * @param int         $page   numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista zasobow spelniajacych kryteria
     */
    public function getFilteredResources(?string $type, array $tagIds, int $page): PaginationInterface;

    /**
     * Znajduje zasób po identyfikatorze.
     *
     * @param int $id identyfikator zasobu
     *
     * @return Resource|null znaleziony zasob albo null
     */
    public function find(int $id): ?Resource;

    /**
     * Zapisuje nowy zasób.
     *
     * @param Resource $resource zasob do zapisania
     */
    public function createResource(Resource $resource): void;

    /**
     * Zatwierdza zmiany w istniejącym zasobie.
     *
     * @param Resource $resource zasob do zaktualizowania
     */
    public function updateResource(Resource $resource): void;

    /**
     * Usuwa zasób.
     *
     * @param Resource $resource zasob do usuniecia
     */
    public function deleteResource(Resource $resource): void;
}
