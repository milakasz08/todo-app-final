<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * Class EmailVerifier.
 */
class EmailVerifier
{
    /**
     * Constructor.
     *
     * @param VerifyEmailHelperInterface $verifyEmailHelper opis parametru.     * @param MailerInterface            $mailer opis parametru.     * @param EntityManagerInterface     $entityManager opis parametru.     */
    public function __construct(private VerifyEmailHelperInterface $verifyEmailHelper, private MailerInterface $mailer, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Send the email confirmation.
     *
     * @param string         $verifyEmailRouteName opis parametru.     * @param User           $user opis parametru.     * @param TemplatedEmail $email opis parametru.     *
     * @return void opis wartosci zwracanej.     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * Handle the email confirmation.
     *
     * @param Request $request opis parametru.     * @param User    $user opis parametru.     *
     * @return void opis wartosci zwracanej.     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
