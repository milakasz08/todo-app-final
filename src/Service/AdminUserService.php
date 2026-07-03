<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AdminUserService.
 */
class AdminUserService implements AdminUserServiceInterface
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param UserRepository         $userRepository repozytorium uzytkownikow
     * @param EntityManagerInterface $entityManager  menedzer encji Doctrine
     * @param PaginatorInterface     $paginator      paginator list
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly EntityManagerInterface $entityManager, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get the paginated list of users.
     *
     * @param int $page numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista uzytkownikow
     */
    public function getPaginatedUsers(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['u.email', 'u.id'],
                'defaultSortFieldName' => 'u.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Nadaje albo odbiera rolę administratora. Zwraca true, gdy po zmianie
     * użytkownik jest administratorem.
     *
     * @param User $user uzytkownik, ktoremu zmieniana jest rola
     *
     * @return bool true, jesli uzytkownik otrzymal role administratora; false, jesli ja utracil
     */
    public function toggleAdminRole(User $user): bool
    {
        $isNowAdmin = !in_array('ROLE_ADMIN', $user->getRoles(), true);

        $user->setRoles($isNowAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);
        $this->entityManager->flush();

        return $isNowAdmin;
    }
}
