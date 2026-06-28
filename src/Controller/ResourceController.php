<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\Resource;
use App\Form\ResourceType;
use App\Repository\ResourceRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/resource')]
/**
 * Class ResourceController.
 */
class ResourceController extends AbstractController
{
    /**
     * Display the list of resources.
     *
     * @param ResourceRepository $resourceRepository repozytorium zasobow
     * @param TagRepository      $tagRepository      repozytorium tagow
     * @param Request            $request            biezace zadanie HTTP
     *
     * @return Response wyrenderowana lista zasobow
     */
    #[Route('/', name: 'app_resource_index', methods: ['GET'])]
    public function index(ResourceRepository $resourceRepository, TagRepository $tagRepository, Request $request): Response
    {
        $type = $request->query->get('type');
        $selectedTagIds = array_map('intval', $request->query->all('tags'));

        // 1. Pobranie zasobów z bazy z filtrem po tagach (jeśli wybrano)
        $qb = $resourceRepository->createQueryBuilder('r');

        if (!empty($selectedTagIds)) {
            $qb->join('r.tags', 't')
                ->andWhere('t.id IN (:tagIds)')
                ->setParameter('tagIds', $selectedTagIds)
                ->groupBy('r.id'); // żeby zasób z wieloma dopasowanymi tagami nie powtarzał się
        }

        $allResources = $qb->getQuery()->getResult();

        // 2. Filtr po typie
        if ($type) {
            $filteredResources = [];
            foreach ($allResources as $resource) {
                $titleLower = mb_strtolower($resource->getTitle() ?: '');

                if ('film' === $type) {
                    if (str_contains($titleLower, 'film') || str_contains($titleLower, 'dvd') || str_contains($titleLower, 'tenenbaums')) {
                        $filteredResources[] = $resource;
                    }
                } elseif ('plyta' === $type) {
                    if (str_contains($titleLower, 'audio') || str_contains($titleLower, 'music') || str_contains($titleLower, 'płyta') || str_contains($titleLower, 'cd') || str_contains($titleLower, 'roses')) {
                        $filteredResources[] = $resource;
                    }
                } elseif ('ksiazka' === $type) {
                    if (!str_contains($titleLower, 'film') && !str_contains($titleLower, 'dvd') && !str_contains($titleLower, 'cd') && !str_contains($titleLower, 'audio') && !str_contains($titleLower, 'roses') && !str_contains($titleLower, 'tenenbaums')) {
                        $filteredResources[] = $resource;
                    }
                }
            }
            $resources = $filteredResources;
        } else {
            $resources = $allResources;
        }

        return $this->render('resource/index.html.twig', [
            'resources' => $resources,
            'allTags' => $tagRepository->findAll(),
            'selectedTagIds' => $selectedTagIds,
            'selectedType' => $type,
        ]);
    }

    /**
     * Create a new resource.
     *
     * @param Request                $request       biezace zadanie HTTP
     * @param EntityManagerInterface $entityManager menedzer encji Doctrine
     *
     * @return Response formularz dodawania albo przekierowanie po zapisie
     */
    #[Route('/new', name: 'app_resource_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resource = new Resource();
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resource);
            $entityManager->flush();

            $this->addFlash('success', 'Pomyślnie dodano nowy zasób do bazy danych.');

            return $this->redirectToRoute('app_resource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resource/new.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }

    /**
     * Show a resource.
     *
     * @param Resource $resource zasob do wyswietlenia
     *
     * @return Response widok szczegolow zasobu
     */
    #[Route('/{id}', name: 'app_resource_show', methods: ['GET'])]
    public function show(Resource $resource): Response
    {
        return $this->render('resource/show.html.twig', [
            'resource' => $resource,
        ]);
    }

    /**
     * Edit a resource.
     *
     * @param Request                $request       biezace zadanie HTTP
     * @param Resource               $resource      zasob do edycji
     * @param EntityManagerInterface $entityManager menedzer encji Doctrine
     *
     * @return Response formularz edycji albo przekierowanie po zapisie
     */
    #[Route('/{id}/edit', name: 'app_resource_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Zmiany w zasobie zostały pomyślnie zapisane.');

            return $this->redirectToRoute('app_resource_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resource/edit.html.twig', [
            'resource' => $resource,
            'form' => $form,
        ]);
    }

    /**
     * Delete a resource.
     *
     * @param Request                $request       biezace zadanie HTTP
     * @param Resource               $resource      zasob do usuniecia
     * @param EntityManagerInterface $entityManager menedzer encji Doctrine
     *
     * @return Response przekierowanie do listy zasobow
     */
    #[Route('/{id}', name: 'app_resource_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resource->getId(), $request->request->get('_token'))) {
            $entityManager->remove($resource);
            $entityManager->flush();
            $this->addFlash('success', 'Zasób został trwale usunięty z magazynu.');
        }

        return $this->redirectToRoute('app_resource_index', [], Response::HTTP_SEE_OTHER);
    }
}
