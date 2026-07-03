<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CategoryServiceInterface.
 */
interface CategoryServiceInterface
{
    /**
     * Get the paginated list of categories.
     *
     * @param int $page numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista kategorii
     */
    public function getPaginatedCategories(int $page): PaginationInterface;

    /**
     * Zapisuje nową kategorię.
     *
     * @param Category $category kategoria do zapisania
     */
    public function createCategory(Category $category): void;

    /**
     * Zatwierdza zmiany w istniejącej kategorii.
     */
    public function updateCategory(): void;

    /**
     * Usuwa kategorię.
     *
     * @param Category $category kategoria do usuniecia
     */
    public function deleteCategory(Category $category): void;
}
