<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Tag;

/**
 * Interface TagServiceInterface.
 */
interface TagServiceInterface
{
    /**
     * Zwraca wszystkie dostepne tagi.
     *
     * @return Tag[]
     */
    public function getAllTags(): array;
}
