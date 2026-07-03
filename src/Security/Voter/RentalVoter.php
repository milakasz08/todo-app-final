<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Security\Voter;

use App\Entity\Rental;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class RentalVoter.
 *
 * Decyduje, czy zalogowany użytkownik może zwrócić dane wypożyczenie.
 * Bez tego każdy zalogowany user mógłby podmienić {id} w adresie
 * i zwrócić cudze wypożyczenie (atak IDOR) - stąd sprawdzenie
 * własności rekordu, a nie tylko samej roli.
 */
class RentalVoter extends Voter
{
    public const RETURN_RENTAL = 'RENTAL_RETURN';

    /**
     * Determine if this voter supports the given attribute and subject.
     *
     * @param string $attribute atrybut uprawnienia (np. RentalVoter::RETURN_RENTAL)
     * @param mixed  $subject   obiekt, ktorego dotyczy sprawdzenie uprawnienia
     *
     * @return bool czy ten voter obsluguje dana pare atrybut/przedmiot
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::RETURN_RENTAL === $attribute && $subject instanceof Rental;
    }

    /**
     * Decide whether access should be granted.
     *
     * @param string         $attribute atrybut uprawnienia
     * @param mixed          $subject   obiekt, ktorego dotyczy sprawdzenie (encja Rental)
     * @param TokenInterface $token     token biezacego uzytkownika
     *
     * @return bool czy dostep powinien zostac przyznany
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Rental $rental */
        $rental = $subject;

        return $rental->getUser() === $user;
    }
}
