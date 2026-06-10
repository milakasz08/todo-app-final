<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Form\ResourceType;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/resource')]
class ResourceController extends AbstractController
{
    #[Route('/', name: 'app_resource_index', methods: ['GET'])]
    public function index(ResourceRepository $resourceRepository, Request $request): Response
    {
        $type = $request->query->get('type');
        $allResources = $resourceRepository->findAll();

        // Jeśli parametr typu został przesłany (?type=...), filtrujemy kolekcję
        if ($type) {
            $filteredResources = [];
            foreach ($allResources as $resource) {
                $titleLower = mb_strtolower($resource->getTitle() ?: '');
                $descLower = mb_strtolower($resource->getDescription() ?: '');

                if ($type === 'film') {
                    // Warunki dopasowania dla filmów
                    if (str_contains($titleLower, 'film') || str_contains($titleLower, 'dvd') || str_contains($descLower, 'reżyser') || str_contains($descLower, 'film') || str_contains($titleLower, 'tenenbaums')) {
                        $filteredResources[] = $resource;
                    }
                } elseif ($type === 'plyta') {
                    // Warunki dopasowania dla płyt muzycznych
                    if (str_contains($titleLower, 'audio') || str_contains($titleLower, 'music') || str_contains($titleLower, 'płyta') || str_contains($titleLower, 'cd') || str_contains($titleLower, 'roses')) {
                        $filteredResources[] = $resource;
                    }
                } elseif ($type === 'ksiazka') {
                    // Jeśli to książka (wszystko co nie pasuje ewidentnie do filmu lub płyty)
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
        ]);
    }

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

    #[Route('/{id}', name: 'app_resource_show', methods: ['GET'])]
    public function show(Resource $resource): Response
    {
        return $this->render('resource/show.html.twig', [
            'resource' => $resource,
        ]);
    }

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
