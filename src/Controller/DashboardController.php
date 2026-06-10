<?php

namespace App\Controller;

use App\Repository\RentalRepository;
use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ResourceRepository $resourceRepository, RentalRepository $rentalRepository): Response
    {
        // 1. Łączna liczba zasobów w systemie
        $totalResources = $resourceRepository->count([]);

        // 2. Liczba aktualnie trwających wypożyczeń
        $activeRentals = $rentalRepository->count(['returnedAt' => null]);

        // 3. Bezpieczne wyciąganie najpopularniejszego zasobu
        $mostPopularData = $rentalRepository->createQueryBuilder('r')
            ->select('IDENTITY(r.resource) as resourceId, COUNT(r.id) as rentalCount')
            ->groupBy('resourceId')
            ->orderBy('rentalCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $mostPopularTitle = 'Brak wypożyczeń';
        $mostPopularCount = 0;

        if ($mostPopularData) {
            $mostPopularCount = $mostPopularData['rentalCount'];
            // Znajdujemy obiekt zasobu po jego ID, dzięki czemu Twig bez problemu go wyświetli
            $resource = $resourceRepository->find($mostPopularData['resourceId']);
            if ($resource) {
                // Jeśli pole to name, spróbuje użyć getName(), jeśli title to getTitle(), a jeśli nic nie zadziała - rzutuje na string
                $mostPopularTitle = method_exists($resource, 'getName') ? $resource->getName() :
                    (method_exists($resource, 'getTitle') ? $resource->getTitle() : (string)$resource);
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
