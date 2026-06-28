<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Repository\RentalRepository;
use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class DashboardController.
 */
final class DashboardController extends AbstractController
{
    /**
     * Display the dashboard.
     *
     * @param ResourceRepository $resourceRepository repozytorium zasobow
     * @param RentalRepository   $rentalRepository   repozytorium wypozyczen
     *
     * @return Response wyrenderowany pulpit
     */
    #[Route('/', name: 'app_dashboard')]
    public function index(ResourceRepository $resourceRepository, RentalRepository $rentalRepository): Response
    {
        // 1. Łączna liczba zasobów w systemie
        $totalResources = $resourceRepository->count([]);

        // 2. Liczba aktualnie trwających wypożyczeń
        $activeRentals = $rentalRepository->count(['returnedAt' => null]);

        // 3. Bezpieczne wyciąganie najpopularniejszego zasobu
        $mostPopularData = $rentalRepository->createQueryBuilder('r')
            ->select('res.id as resourceId', 'COUNT(r.id) as rentalCount')
            ->join('r.resource', 'res')
            ->groupBy('res.id')
            ->orderBy('rentalCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $mostPopularTitle = 'Brak wypożyczeń';
        $mostPopularCount = 0;

        if ($mostPopularData) {
            $mostPopularCount = $mostPopularData['rentalCount'];
            $resource = $resourceRepository->find($mostPopularData['resourceId']);

            if ($resource) {
                $mostPopularTitle = method_exists($resource, 'getTitle') ? $resource->getTitle() : (string) $resource;
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'total_resources' => $totalResources,
            'active_rentals' => $activeRentals,
            'most_popular_title' => $mostPopularTitle,
            'most_popular_count' => $mostPopularCount,
        ]);
    }
}
