<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\Resource;
use App\Form\ResourceType;
use App\Service\ResourceServiceInterface;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/resource')]
/**
 * Class ResourceController.
 */
class ResourceController extends AbstractController
{
    /**
     * Display the paginated, filterable list of resources.
     *
     * @param ResourceServiceInterface $resourceService serwis obslugujacy zasoby
     * @param TagServiceInterface      $tagService      serwis obslugujacy tagi
     * @param Request                  $request         biezace zadanie HTTP
     * @param int                      $page            numer strony (paginacja)
     *
     * @return Response wyrenderowana lista zasobow
     */
    #[Route('/', name: 'app_resource_index', methods: ['GET'])]
    public function index(ResourceServiceInterface $resourceService, TagServiceInterface $tagService, Request $request, #[MapQueryParameter] int $page = 1): Response
    {
        $type = $request->query->get('type');
        $selectedTagIds = array_map('intval', $request->query->all('tags'));

        return $this->render('resource/index.html.twig', [
            'pagination' => $resourceService->getFilteredResources($type, $selectedTagIds, $page),
            'allTags' => $tagService->getAllTags(),
            'selectedTagIds' => $selectedTagIds,
            'selectedType' => $type,
        ]);
    }

    /**
     * Create a new resource.
     *
     * @param Request                  $request         biezace zadanie HTTP
     * @param ResourceServiceInterface $resourceService serwis obslugujacy zasoby
     *
     * @return Response formularz dodawania albo przekierowanie po zapisie
     */
    #[Route('/new', name: 'app_resource_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, ResourceServiceInterface $resourceService): Response
    {
        $resource = new Resource();
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resourceService->createResource($resource);

            $this->addFlash('success', 'flash.resource.created');

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
     * @param Request                  $request         biezace zadanie HTTP
     * @param Resource                 $resource        zasob do edycji
     * @param ResourceServiceInterface $resourceService serwis obslugujacy zasoby
     *
     * @return Response formularz edycji albo przekierowanie po zapisie
     */
    #[Route('/{id}/edit', name: 'app_resource_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Resource $resource, ResourceServiceInterface $resourceService): Response
    {
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resourceService->updateResource($resource);

            $this->addFlash('success', 'flash.resource.updated');

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
     * @param Request                  $request         biezace zadanie HTTP
     * @param Resource                 $resource        zasob do usuniecia
     * @param ResourceServiceInterface $resourceService serwis obslugujacy zasoby
     *
     * @return Response przekierowanie do listy zasobow
     */
    #[Route('/{id}', name: 'app_resource_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Resource $resource, ResourceServiceInterface $resourceService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resource->getId(), $request->request->get('_token'))) {
            $resourceService->deleteResource($resource);
            $this->addFlash('success', 'flash.resource.deleted');
        }

        return $this->redirectToRoute('app_resource_index', [], Response::HTTP_SEE_OTHER);
    }
}
