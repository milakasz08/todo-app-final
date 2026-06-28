<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\Rental;
use App\Form\RentalType;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rental')]
/**
 * Class RentalController.
 */
class RentalController extends AbstractController
{
    /**
     * Display the list of rentals.
     *
     * @param RentalRepository $rentalRepository
     *
     * @return Response
     */
    #[Route('/', name: 'app_rental_index', methods: ['GET'])]
    public function index(RentalRepository $rentalRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $rentals = $rentalRepository->findAll();
        } else {
            $rentals = $rentalRepository->findBy(['user' => $this->getUser()]);
        }

        return $this->render('rental/index.html.twig', [
            'rentals' => $rentals,
        ]);
    }

    /**
     * Create a new rental.
     *
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/new', name: 'app_rental_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rental = new Rental();

        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        $rental->setUser($currentUser);
        $rental->setBorrowerName($currentUser->getEmail());
        $rental->setRentedAt(new \DateTimeImmutable());
        $rental->setStatus('PENDING');

        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resource = $rental->getResource();

            if ($resource->getQuantity() < $rental->getQuantity()) {
                $this->addFlash('error', 'Niestety, brak żądanej ilości sztuk w magazynie.');

                return $this->render('rental/new.html.twig', [
                    'rental' => $rental,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($rental);
            $entityManager->flush();

            $this->addFlash('success', 'Wniosek o rezerwację został złożony i oczekuje na weryfikację przez administratora.');

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
     * @param Rental                 $rental
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}/approve', name: 'app_rental_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Rental $rental, EntityManagerInterface $entityManager): Response
    {
        if ('PENDING' !== $rental->getStatus()) {
            $this->addFlash('error', 'Można zatwierdzić tylko wnioski oczekujące.');

            return $this->redirectToRoute('app_rental_index');
        }

        $resource = $rental->getResource();

        if ($resource->getQuantity() < $rental->getQuantity()) {
            $this->addFlash('error', 'Brak wystarczającej ilości sztuk w magazynie, by zatwierdzić to wypożyczenie!');

            return $this->redirectToRoute('app_rental_index');
        }

        $rental->setStatus('APPROVED');
        $resource->setQuantity($resource->getQuantity() - $rental->getQuantity());

        $entityManager->flush();

        $this->addFlash('success', 'Wypożyczenie zostało pomyślnie zatwierdzone. Stan magazynowy zaktualizowany.');

        return $this->redirectToRoute('app_rental_index');
    }

    /**
     * Reject a rental.
     *
     * @param Rental                 $rental
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}/reject', name: 'app_rental_reject', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reject(Rental $rental, EntityManagerInterface $entityManager): Response
    {
        if ('PENDING' !== $rental->getStatus()) {
            $this->addFlash('error', 'Można odrzucić tylko wnioski oczekujące.');

            return $this->redirectToRoute('app_rental_index');
        }

        $rental->setStatus('REJECTED');
        $entityManager->flush();

        $this->addFlash('success', 'Wniosek o rezerwację został odrzucony.');

        return $this->redirectToRoute('app_rental_index');
    }

    /**
     * Return a rented resource.
     *
     * @param Rental                 $rental
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}/return', name: 'app_rental_return', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function return(Rental $rental, EntityManagerInterface $entityManager): Response
    {
        // Zwrócić może tylko właściciel wypożyczenia albo admin
        if (!$this->isGranted('ROLE_ADMIN') && $rental->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Nie możesz zwrócić cudzego wypożyczenia.');
        }

        if ('APPROVED' !== $rental->getStatus()) {
            $this->addFlash('error', 'Można zwrócić tylko zasób, który jest aktualnie wypożyczony (zatwierdzony).');

            return $this->redirectToRoute('app_rental_index');
        }

        $resource = $rental->getResource();
        $resource->setQuantity($resource->getQuantity() + $rental->getQuantity());

        $rental->setStatus('RETURNED');
        $rental->setReturnedAt(new \DateTime());

        $entityManager->flush();

        $this->addFlash('success', 'Zasób został zwrócony. Stan magazynowy zaktualizowany.');

        return $this->redirectToRoute('app_rental_index');
    }
}
