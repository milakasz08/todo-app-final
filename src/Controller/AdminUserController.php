<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]

/**
 * Class AdminUserController.
 */
class AdminUserController extends AbstractController
{
    /**
     * Display the list of users.
     *
     * @param EntityManagerInterface $entityManager menedzer encji Doctrine
     *
     * @return Response wyrenderowana lista uzytkownikow
     */
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Toggle the admin role of a user.
     *
     * @param User                    $user          uzytkownik, ktoremu zmieniana jest rola
     * @param EntityManagerInterface $entityManager menedzer encji Doctrine
     *
     * @return Response przekierowanie do listy uzytkownikow
     */
    #[Route('/{id}/toggle-admin', name: 'app_admin_user_toggle', methods: ['POST'])]
    public function toggleAdmin(User $user, EntityManagerInterface $entityManager): Response
    {
        // Zabezpieczenie przed odebraniem admina samemu sobie
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Nie możesz odebrać uprawnień samemu sobie!');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $user->setRoles(['ROLE_USER']);
            $this->addFlash('success', 'Odebrano uprawnienia administratora użytkownikowi '.$user->getEmail());
        } else {
            $user->setRoles(['ROLE_ADMIN']);
            $this->addFlash('success', 'Nadano uprawnienia administratora użytkownikowi '.$user->getEmail());
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user_index');
    }
}
