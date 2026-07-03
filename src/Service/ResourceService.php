<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Resource;
use App\Enum\MediaType;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ResourceService.
 */
class ResourceService implements ResourceServiceInterface
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ResourceRepository     $resourceRepository repozytorium zasobow
     * @param EntityManagerInterface $entityManager       menedzer encji Doctrine
     * @param PaginatorInterface     $paginator           paginator list
     */
    public function __construct(private readonly ResourceRepository $resourceRepository, private readonly EntityManagerInterface $entityManager, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Zamienia string z parametru zapytania na wartość enuma MediaType
     * (nieznana/pusta wartość oznacza brak filtra), buduje zapytanie
     * w repozytorium i zwraca gotową, posortowaną stronę wyników.
     *
     * @param string|null $type   identyfikator typu zasobu przekazany w zapytaniu (wartosc enuma MediaType)
     * @param int[]       $tagIds identyfikatory tagow do filtrowania
     * @param int         $page   numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista zasobow spelniajacych kryteria
     */
    public function getFilteredResources(?string $type, array $tagIds, int $page): PaginationInterface
    {
        $mediaType = null !== $type ? MediaType::tryFrom($type) : null;

        return $this->paginator->paginate(
            $this->resourceRepository->queryFiltered($mediaType, $tagIds),
            $page,
            self::ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['r.title', 'r.author', 'r.quantity', 'r.id'],
                'defaultSortFieldName' => 'r.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Znajduje zasób po identyfikatorze.
     *
     * @param int $id identyfikator zasobu
     *
     * @return Resource|null znaleziony zasob albo null
     */
    public function find(int $id): ?Resource
    {
        return $this->resourceRepository->find($id);
    }

    /**
     * Zapisuje nowy zasób.
     *
     * @param Resource $resource zasob do zapisania
     */
    public function createResource(Resource $resource): void
    {
        $this->entityManager->persist($resource);
        $this->entityManager->flush();
    }

    /**
     * Zasob jest juz zarzadzany przez Doctrine (pochodzi z formularza edycji),
     * wiec wystarczy zatwierdzic zmiany.
     *
     * @param Resource $resource zasob do zaktualizowania
     */
    public function updateResource(Resource $resource): void
    {
        $this->entityManager->flush();
    }

    /**
     * Usuwa zasób.
     *
     * @param Resource $resource zasob do usuniecia
     */
    public function deleteResource(Resource $resource): void
    {
        $this->entityManager->remove($resource);
        $this->entityManager->flush();
    }
}
