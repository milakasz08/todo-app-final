<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface AdminUserServiceInterface.
 */
interface AdminUserServiceInterface
{
    /**
     * Get the paginated list of users.
     *
     * @param int $page numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista uzytkownikow
     */
    public function getPaginatedUsers(int $page): PaginationInterface;

    /**
     * Zmienia rolę użytkownika na przeciwną (user <-> admin).
     *
     * @param User $user uzytkownik, ktoremu zmieniana jest rola
     *
     * @return bool true, jesli uzytkownik otrzymal role administratora; false, jesli ja utracil
     */
    public function toggleAdminRole(User $user): bool;
}
