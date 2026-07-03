<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Exception;

/**
 * Class RentalException.
 *
 * Rzucany przez warstwe serwisow, gdy operacja na wypozyczeniu jest
 * niedozwolona z punktu widzenia logiki biznesowej (np. brak zasobu,
 * niewystarczajaca ilosc sztuk, nieprawidlowy status).
 */
class RentalException extends \RuntimeException
{
}
