<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Service\ProfileServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
/**
 * Class ProfileController.
 *
 * Pozwala kazdemu zalogowanemu uzytkownikowi zmienic wlasny adres e-mail
 * oraz haslo.
 */
class ProfileController extends AbstractController
{
    /**
     * Edit the current user's own data.
     *
     * @param Request                 $request        biezace zadanie HTTP
     * @param ProfileServiceInterface $profileService serwis obslugujacy edycje profilu
     *
     * @return Response formularz edycji danych albo przekierowanie po zapisie
     */
    #[Route('', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProfileServiceInterface $profileService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string|null $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $profileService->updateProfile($user, $plainPassword);

            $this->addFlash('success', 'flash.profile.updated');

            return $this->redirectToRoute('app_profile_edit');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
