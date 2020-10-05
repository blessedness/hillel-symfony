<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FacebookAuthenticator extends SocialAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     * @param  Request  $request
     * @param  AuthenticationException|null  $authException
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('app_login'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'oauth_login_facebook_callback';
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    private function getFacebookClient()
    {
        return $this->clientRegistry->getClient('facebook');
    }

    /**
     * @param  mixed  $credentials
     * @param  UserProviderInterface  $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);

        $email = $facebookUser->getEmail();

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(
                $this->encoder->encodePassword($user, uniqid())
            );

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param  Request  $request
     * @param  AuthenticationException  $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param  Request  $request
     * @param  TokenInterface  $token
     * @param  string  $providerKey
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('home')
        );
    }
}
