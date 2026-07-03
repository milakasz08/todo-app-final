<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CategoryRepository     $categoryRepository repozytorium kategorii
     * @param EntityManagerInterface $entityManager       menedzer encji Doctrine
     * @param PaginatorInterface     $paginator           paginator list
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly EntityManagerInterface $entityManager, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get the paginated list of categories.
     *
     * @param int $page numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista kategorii
     */
    public function getPaginatedCategories(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            self::ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['c.name', 'c.id'],
                'defaultSortFieldName' => 'c.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Zapisuje nowo utworzoną kategorię.
     *
     * @param Category $category kategoria do zapisania
     */
    public function createCategory(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * Zatwierdza zmiany wprowadzone w istniejącej kategorii (encja jest już
     * zarządzana przez Doctrine, więc wystarczy flush).
     */
    public function updateCategory(): void
    {
        $this->entityManager->flush();
    }

    /**
     * Usuwa kategorię z bazy.
     *
     * @param Category $category kategoria do usuniecia
     */
    public function deleteCategory(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
