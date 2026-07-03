<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\User;
use App\Service\AdminUserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]

/**
 * Class AdminUserController.
 */
class AdminUserController extends AbstractController
{
    /**
     * Display the paginated list of users.
     *
     * @param AdminUserServiceInterface $adminUserService serwis zarzadzania uzytkownikami
     * @param int                       $page             numer strony (paginacja)
     *
     * @return Response wyrenderowana lista uzytkownikow
     */
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(AdminUserServiceInterface $adminUserService, #[MapQueryParameter] int $page = 1): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'pagination' => $adminUserService->getPaginatedUsers($page),
        ]);
    }

    /**
     * Toggle the admin role of a user.
     *
     * @param User                      $user             uzytkownik, ktoremu zmieniana jest rola
     * @param AdminUserServiceInterface $adminUserService serwis zarzadzania uzytkownikami
     * @param TranslatorInterface       $translator       tlumacz komunikatow
     *
     * @return Response przekierowanie do listy uzytkownikow
     */
    #[Route('/{id}/toggle-admin', name: 'app_admin_user_toggle', methods: ['POST'])]
    public function toggleAdmin(User $user, AdminUserServiceInterface $adminUserService, TranslatorInterface $translator): Response
    {
        // Zabezpieczenie przed odebraniem admina samemu sobie
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'flash.admin.cannot_demote_self');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $isNowAdmin = $adminUserService->toggleAdminRole($user);
        $flashKey = $isNowAdmin ? 'flash.admin.promoted' : 'flash.admin.demoted';

        $this->addFlash('success', $translator->trans($flashKey, ['%email%' => $user->getEmail()]));

        return $this->redirectToRoute('app_admin_user_index');
    }
}
