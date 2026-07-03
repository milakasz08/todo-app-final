<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

/**
 * Class TagService.
 */
class TagService implements TagServiceInterface
{
    /**
     * @param TagRepository $tagRepository repozytorium tagow
     */
    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    /**
     * Zwraca wszystkie dostepne tagi (uzywane m.in. do filtrowania katalogu).
     *
     * @return Tag[]
     */
    public function getAllTags(): array
    {
        return $this->tagRepository->findAll();
    }
}
