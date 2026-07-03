<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

/**
 * Interface DashboardServiceInterface.
 */
interface DashboardServiceInterface
{
    /**
     * @return array{totalResources: int, activeRentals: int, mostPopularTitle: string, mostPopularCount: int}
     */
    public function getStatistics(): array;
}
