<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Repository\RentalRepository;
use App\Repository\ResourceRepository;

/**
 * Class DashboardService.
 */
class DashboardService implements DashboardServiceInterface
{
    /**
     * Constructor.
     *
     * @param ResourceRepository $resourceRepository repozytorium zasobow
     * @param RentalRepository   $rentalRepository   repozytorium wypozyczen
     */
    public function __construct(private readonly ResourceRepository $resourceRepository, private readonly RentalRepository $rentalRepository)
    {
    }

    /**
     * Zbiera dane potrzebne na pulpicie: liczbę zasobów, liczbę aktywnych
     * wypożyczeń oraz tytuł i liczbę wypożyczeń najpopularniejszego zasobu.
     *
     * @return array{totalResources: int, activeRentals: int, mostPopularTitle: string, mostPopularCount: int} statystyki pulpitu
     */
    public function getStatistics(): array
    {
        $totalResources = $this->resourceRepository->count([]);
        $activeRentals = $this->rentalRepository->countActive();

        $mostPopularData = $this->rentalRepository->findMostPopularResourceData();

        $mostPopularTitle = 'Brak wypożyczeń';
        $mostPopularCount = 0;

        if (null !== $mostPopularData) {
            $mostPopularCount = (int) $mostPopularData['rentalCount'];
            $resource = $this->resourceRepository->find($mostPopularData['resourceId']);

            if (null !== $resource) {
                $mostPopularTitle = $resource->getTitle() ?? $mostPopularTitle;
            }
        }

        return [
            'totalResources' => $totalResources,
            'activeRentals' => $activeRentals,
            'mostPopularTitle' => $mostPopularTitle,
            'mostPopularCount' => $mostPopularCount,
        ];
    }
}
