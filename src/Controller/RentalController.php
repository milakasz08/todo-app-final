<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Form\RentalType;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rental')]
class RentalController extends AbstractController
{
    #[Route('/', name: 'app_rental_index', methods: ['GET'])]
    public function index(RentalRepository $rentalRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isGranted('ROLE_ADMIN')) {
            $rentals = $rentalRepository->findAll();
        } else {
            $rentals = $rentalRepository->findBy(['user' => $this->getUser()]);
        }

        return $this->render('rental/index.html.twig', [
            'rentals' => $rentals,
        ]);
    }

    #[Route('/new', name: 'app_rental_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $rental = new Rental();
        $rental->setRentedAt(new \DateTimeImmutable());

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $rental->setUser($user);
        $rental->setBorrowerName($user->getEmail());

        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resource = $rental->getResource();
            $requestedQuantity = $rental->getQuantity();

            if ($requestedQuantity === null || $requestedQuantity <= 0) {
                $requestedQuantity = 1;
                $rental->setQuantity(1);
            }

            if ($resource->getQuantity() >= $requestedQuantity) {
                $resource->setQuantity($resource->getQuantity() - $requestedQuantity);

                $entityManager->persist($rental);
                $entityManager->flush();

                return $this->redirectToRoute('app_rental_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', 'Brak wystarczającej ilości tego zasobu na stanie magazynu!');
            }
        }

        return $this->render('rental/new.html.twig', [
            'rental' => $rental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rental_show', methods: ['GET'])]
    public function show(Rental $rental): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_ADMIN') && $rental->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do przeglądania tego wypożyczenia.');
        }

        return $this->render('rental/show.html.twig', [
            'rental' => $rental,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rental_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rental $rental, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_ADMIN') && $rental->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Nie możesz edytować tego wypożyczenia.');
        }

        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rental/edit.html.twig', [
            'rental' => $rental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rental_delete', methods: ['POST'])]
    public function delete(Request $request, Rental $rental, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Tylko administrator może usuwać rekordy wypożyczeń.');

        if ($this->isCsrfTokenValid('delete'.$rental->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rental);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rental_index', [], Response::HTTP_SEE_OTHER);
    }
}
