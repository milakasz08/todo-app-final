<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Service\DashboardServiceInterface;
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
     * @param DashboardServiceInterface $dashboardService serwis dostarczajacy statystyki pulpitu
     *
     * @return Response wyrenderowany pulpit
     */
    #[Route('/', name: 'app_dashboard')]
    public function index(DashboardServiceInterface $dashboardService): Response
    {
        $statistics = $dashboardService->getStatistics();

        return $this->render('dashboard/index.html.twig', [
            'total_resources' => $statistics['totalResources'],
            'active_rentals' => $statistics['activeRentals'],
            'most_popular_title' => $statistics['mostPopularTitle'],
            'most_popular_count' => $statistics['mostPopularCount'],
        ]);
    }
}
