<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\RegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{
    /**
     * Register a new user.
     *
     * @param Request                      $request             biezace zadanie HTTP
     * @param RegistrationServiceInterface $registrationService serwis rejestracji uzytkownikow
     *
     * @return Response formularz rejestracji albo przekierowanie po zapisie
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, RegistrationServiceInterface $registrationService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $registrationService->register($user, $plainPassword);

            $this->addFlash('success', 'flash.registration.success');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
