<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class AppAuthenticator.
 */
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator generator adresow URL
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Authenticate the request.
     *
     * @param Request $request biezace zadanie HTTP
     *
     * @return Passport dane uwierzytelniajace uzytkownika
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Handle successful authentication.
     *
     * @param Request        $request      biezace zadanie HTTP
     * @param TokenInterface $token        token uwierzytelnienia
     * @param string         $firewallName nazwa zapory (firewalla) bezpieczenstwa
     *
     * @return Response|null odpowiedz przekierowujaca po zalogowaniu
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Przekierowanie na stronę główną/pulpit po zalogowaniu
        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }

    /**
     * Get the login URL.
     *
     * @param Request $request biezace zadanie HTTP
     *
     * @return string adres URL strony logowania
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
