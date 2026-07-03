<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Rental;
use App\Entity\User;
use App\Exception\RentalException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface RentalServiceInterface.
 */
interface RentalServiceInterface
{
    /**
     * Get the paginated list of rentals visible for the given user (all of
     * them for an admin, own ones otherwise).
     *
     * @param User $user    biezacy uzytkownik
     * @param bool $isAdmin czy uzytkownik ma uprawnienia administratora
     * @param int  $page    numer strony (paginacja)
     *
     * @return PaginationInterface stronicowana lista wypozyczen widocznych dla uzytkownika
     */
    public function getVisibleRentals(User $user, bool $isAdmin, int $page): PaginationInterface;

    /**
     * Przygotowuje nowy wniosek o wypożyczenie (jeszcze niezapisany).
     *
     * @param User $user uzytkownik skladajacy wniosek
     *
     * @return Rental przygotowany, niezapisany wniosek o wypozyczenie
     */
    public function createNewRentalFor(User $user): Rental;

    /**
     * @param Rental $rental wniosek o wypozyczenie (juz przeszedl walidacje formularza)
     *
     * @throws RentalException gdy zasob nie zostal wybrany albo brakuje sztuk w magazynie
     */
    public function requestRental(Rental $rental): void;

    /**
     * @param Rental $rental wypozyczenie do zatwierdzenia
     *
     * @throws RentalException gdy wniosek nie oczekuje na zatwierdzenie albo brakuje sztuk w magazynie
     */
    public function approve(Rental $rental): void;

    /**
     * @param Rental $rental wypozyczenie do odrzucenia
     *
     * @throws RentalException gdy wniosek nie oczekuje na weryfikację
     */
    public function reject(Rental $rental): void;

    /**
     * @param Rental $rental wypozyczenie do zwrotu
     *
     * @throws RentalException gdy wypozyczenie nie jest aktualnie zatwierdzone
     */
    public function returnRental(Rental $rental): void;
}
