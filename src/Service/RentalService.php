<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Rental;
use App\Entity\User;
use App\Enum\RentalStatus;
use App\Exception\RentalException;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RentalService.
 */
class RentalService implements RentalServiceInterface
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param RentalRepository       $rentalRepository repozytorium wypozyczen
     * @param EntityManagerInterface $entityManager     menedzer encji Doctrine
     * @param PaginatorInterface     $paginator         paginator list
     */
    public function __construct(private readonly RentalRepository $rentalRepository, private readonly EntityManagerInterface $entityManager, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Admin widzi wszystkie wypożyczenia, zwykły użytkownik tylko swoje -
     * w obu przypadkach jako posortowana, stronicowana lista.
     *
     * @param User $user    biezacy uzytkownik
     * @param bool $isAdmin czy uzytkownik ma uprawnienia administratora
     * @param int  $page    numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista wypozyczen widocznych dla uzytkownika
     */
    public function getVisibleRentals(User $user, bool $isAdmin, int $page): PaginationInterface
    {
        $query = $isAdmin
            ? $this->rentalRepository->queryAll()
            : $this->rentalRepository->queryForUser($user);

        return $this->paginator->paginate(
            $query,
            $page,
            self::ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['r.rentedAt', 'r.status', 'r.quantity'],
                'defaultSortFieldName' => 'r.rentedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Przygotowuje nowy, jeszcze niezapisany wniosek o wypożyczenie.
     *
     * @param User $user uzytkownik skladajacy wniosek
     *
     * @return Rental przygotowany, niezapisany wniosek o wypozyczenie
     */
    public function createNewRentalFor(User $user): Rental
    {
        $rental = new Rental();
        $rental->setUser($user);
        $rental->setBorrowerName($user->getEmail());
        $rental->setRentedAt(new \DateTimeImmutable());
        $rental->setStatus(RentalStatus::PENDING);

        return $rental;
    }

    /**
     * @param Rental $rental wniosek o wypozyczenie (juz przeszedl walidacje formularza)
     *
     * @throws RentalException gdy zasob nie zostal wybrany albo brakuje sztuk w magazynie
     */
    public function requestRental(Rental $rental): void
    {
        $resource = $rental->getResource();

        // Zabezpieczenie dodatkowe (obok walidacji formularza) na wypadek,
        // gdyby do serwisu trafil wniosek bez wybranego zasobu.
        if (null === $resource) {
            throw new RentalException('rental.error.no_resource');
        }

        if ($resource->getQuantity() < $rental->getQuantity()) {
            throw new RentalException('rental.error.not_enough_stock');
        }

        $this->entityManager->persist($rental);
        $this->entityManager->flush();
    }

    /**
     * Zatwierdza oczekujący wniosek i zdejmuje odpowiednią ilość z magazynu.
     *
     * @param Rental $rental wypozyczenie do zatwierdzenia
     *
     * @throws RentalException gdy wniosek nie oczekuje na zatwierdzenie albo brakuje sztuk w magazynie
     */
    public function approve(Rental $rental): void
    {
        if (RentalStatus::PENDING !== $rental->getStatus()) {
            throw new RentalException('rental.error.approve_not_pending');
        }

        $resource = $rental->getResource();

        if (null === $resource || $resource->getQuantity() < $rental->getQuantity()) {
            throw new RentalException('rental.error.approve_not_enough_stock');
        }

        $rental->setStatus(RentalStatus::APPROVED);
        $resource->setQuantity($resource->getQuantity() - $rental->getQuantity());

        $this->entityManager->flush();
    }

    /**
     * @param Rental $rental wypozyczenie do odrzucenia
     *
     * @throws RentalException gdy wniosek nie oczekuje na weryfikację
     */
    public function reject(Rental $rental): void
    {
        if (RentalStatus::PENDING !== $rental->getStatus()) {
            throw new RentalException('rental.error.reject_not_pending');
        }

        $rental->setStatus(RentalStatus::REJECTED);
        $this->entityManager->flush();
    }

    /**
     * Przyjmuje zwrot zasobu i oddaje zajęte egzemplarze do magazynu.
     *
     * @param Rental $rental wypozyczenie do zwrotu
     *
     * @throws RentalException gdy wypozyczenie nie jest aktualnie zatwierdzone
     */
    public function returnRental(Rental $rental): void
    {
        if (RentalStatus::APPROVED !== $rental->getStatus()) {
            throw new RentalException('rental.error.return_not_approved');
        }

        $resource = $rental->getResource();

        if (null !== $resource) {
            $resource->setQuantity($resource->getQuantity() + $rental->getQuantity());
        }

        $rental->setStatus(RentalStatus::RETURNED);
        $rental->setReturnedAt(new \DateTime());

        $this->entityManager->flush();
    }
}
