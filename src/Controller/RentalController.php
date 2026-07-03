<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\Rental;
use App\Entity\User;
use App\Exception\RentalException;
use App\Form\RentalType;
use App\Security\Voter\RentalVoter;
use App\Service\RentalServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rental')]
/**
 * Class RentalController.
 */
class RentalController extends AbstractController
{
    /**
     * Display the paginated list of rentals (own ones for a regular user,
     * all of them for an admin).
     *
     * @param RentalServiceInterface $rentalService serwis obslugujacy wypozyczenia
     * @param int                    $page          numer strony (paginacja)
     *
     * @return Response wyrenderowana lista wypozyczen
     */
    #[Route('/', name: 'app_rental_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RentalServiceInterface $rentalService, #[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render('rental/index.html.twig', [
            'pagination' => $rentalService->getVisibleRentals($currentUser, $this->isGranted('ROLE_ADMIN'), $page),
        ]);
    }

    /**
     * Create a new rental.
     *
     * @param Request                $request       biezace zadanie HTTP
     * @param RentalServiceInterface $rentalService serwis obslugujacy wypozyczenia
     *
     * @return Response formularz rezerwacji albo przekierowanie po zapisie
     */
    #[Route('/new', name: 'app_rental_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, RentalServiceInterface $rentalService): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $rental = $rentalService->createNewRentalFor($currentUser);

        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $rentalService->requestRental($rental);
            } catch (RentalException $exception) {
                $this->addFlash('error', $exception->getMessage());

                return $this->render('rental/new.html.twig', [
                    'rental' => $rental,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'flash.rental.created');

            return $this->redirectToRoute('app_rental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rental/new.html.twig', [
            'rental' => $rental,
            'form' => $form,
        ]);
    }

    /**
     * Approve a rental.
     *
     * @param Rental                 $rental        wypozyczenie do zatwierdzenia
     * @param RentalServiceInterface $rentalService serwis obslugujacy wypozyczenia
     *
     * @return Response przekierowanie do listy wypozyczen
     */
    #[Route('/{id}/approve', name: 'app_rental_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Rental $rental, RentalServiceInterface $rentalService): Response
    {
        try {
            $rentalService->approve($rental);
            $this->addFlash('success', 'flash.rental.approved');
        } catch (RentalException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_rental_index');
    }

    /**
     * Reject a rental.
     *
     * @param Rental                 $rental        wypozyczenie do odrzucenia
     * @param RentalServiceInterface $rentalService serwis obslugujacy wypozyczenia
     *
     * @return Response przekierowanie do listy wypozyczen
     */
    #[Route('/{id}/reject', name: 'app_rental_reject', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reject(Rental $rental, RentalServiceInterface $rentalService): Response
    {
        try {
            $rentalService->reject($rental);
            $this->addFlash('success', 'flash.rental.rejected');
        } catch (RentalException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_rental_index');
    }

    /**
     * Return a rented resource.
     *
     * @param Rental                 $rental        wypozyczenie do zwrotu
     * @param RentalServiceInterface $rentalService serwis obslugujacy wypozyczenia
     *
     * @return Response przekierowanie do listy wypozyczen
     */
    #[Route('/{id}/return', name: 'app_rental_return', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function return(Rental $rental, RentalServiceInterface $rentalService): Response
    {
        $this->denyAccessUnlessGranted(RentalVoter::RETURN_RENTAL, $rental);

        try {
            $rentalService->returnRental($rental);
            $this->addFlash('success', 'flash.rental.returned');
        } catch (RentalException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_rental_index');
    }
}
