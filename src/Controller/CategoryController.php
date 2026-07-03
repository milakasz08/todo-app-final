<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category')]
/**
 * Class CategoryController.
 */
final class CategoryController extends AbstractController
{
    /**
     * Display the paginated list of categories.
     *
     * @param CategoryServiceInterface $categoryService serwis zarzadzania kategoriami
     * @param int                      $page            numer strony (paginacja)
     *
     * @return Response wyrenderowana lista kategorii
     */
    #[Route(name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryServiceInterface $categoryService, #[MapQueryParameter] int $page = 1): Response
    {
        return $this->render('category/index.html.twig', [
            'pagination' => $categoryService->getPaginatedCategories($page),
        ]);
    }

    /**
     * Create a new category.
     *
     * @param Request                  $request         biezace zadanie HTTP
     * @param CategoryServiceInterface $categoryService serwis zarzadzania kategoriami
     *
     * @return Response formularz dodawania albo przekierowanie po zapisie
     */
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, CategoryServiceInterface $categoryService): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryService->createCategory($category);

            $this->addFlash('success', 'flash.category.created');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Show a category.
     *
     * @param Category $category kategoria do wyswietlenia
     *
     * @return Response widok szczegolow kategorii
     */
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Edit a category.
     *
     * @param Request                  $request         biezace zadanie HTTP
     * @param Category                 $category        kategoria do edycji
     * @param CategoryServiceInterface $categoryService serwis zarzadzania kategoriami
     *
     * @return Response formularz edycji albo przekierowanie po zapisie
     */
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Category $category, CategoryServiceInterface $categoryService): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryService->updateCategory();

            $this->addFlash('success', 'flash.category.updated');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Delete a category.
     *
     * @param Request                  $request         biezace zadanie HTTP
     * @param Category                 $category        kategoria do usuniecia
     * @param CategoryServiceInterface $categoryService serwis zarzadzania kategoriami
     *
     * @return Response przekierowanie do listy kategorii
     */
    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category, CategoryServiceInterface $categoryService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'))) {
            $categoryService->deleteCategory($category);
            $this->addFlash('success', 'flash.category.deleted');
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
