<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Repository\UserRepository;
class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(UserRepository $userRepository,private UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepository= $userRepository;

    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        $request->getSession()->set(Security::LAST_USERNAME, $email);
        if ($user->isIsBlocked()) {
            throw new CustomUserMessageAuthenticationException('Votre compte a été bloqué. Veuillez attendre le administrateur pour débloquer.');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:

        $user = $token->getUser();
        if(in_array('ROLE_CLIENT', $user->getRoles(), true)){
            return new RedirectResponse($this->urlGenerator->generate('app_client'));
        }
        elseif (in_array('ROLE_FREELANCER', $user->getRoles(), true)){
            return new RedirectResponse($this->urlGenerator->generate('app_freelancer'));
        }
        else{
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        }



        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
