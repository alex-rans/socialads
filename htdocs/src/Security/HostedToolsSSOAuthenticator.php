<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
//use Symfony\Component\Security\Core\Security; // depricated
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class HostedToolsSSOAuthenticator extends AbstractAuthenticator
{
    private string $host;

    public function __construct(
        RouterInterface                         $router,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security               $security,
        private readonly string                 $symfonyEnvironment = 'dev',
    ) {
        $this->host = $router->getContext()->getHost();
    }

    public function supports(Request $request): ?bool
    {
        // Do not log in on every request because Symfony will migrate the user's session a new session id
        if (!empty($this->security->getToken()) && $this->security->getToken()->getUser()) {
            return false;
        }

        if ('dev' === $this->symfonyEnvironment) {
            // Voor development (impersonate)
            return true;
        }
        return $request->cookies->has('hosted-tools-auth-2');
    }


    public function authenticate(Request $request): Passport
    {
        $authCookie = (string) $request->cookies->get('hosted-tools-auth-2');
//        $authCookie = (string) "236850AF277814D5690FE461B385415FB16AC073B24237C98BC6F2E085DABF13";


        $user = $this->_getUser($authCookie);

        return new Passport(
            new UserBadge($user->getName()),
            new CustomCredentials(function($authCookie){
                return true;
            }, $authCookie)
        );
//        return new SelfValidatingPassport(new UserBadge($user->getName()));
    }

    private function _getUser(string $authCookie)
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        if ('dev' === $this->symfonyEnvironment) {
            /** @var User|null $user */
            $user = $userRepository->find(1);

            // Voor development (impersonate)
            return $user;
        }

        // Check if already logged in
        if (!empty($this->security->getToken()) && $this->security->getToken()->getUser()) {
            return $this->security->getUser();
         }

        if (empty($authCookie)) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // Call the iO Tools authentication API
        $curl = curl_init('https://auth.hosted-tools.com/api/user-for-auth-cookie/'.$authCookie.'/'.$this->host);
        if (!$curl) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $authUser = json_decode((string) curl_exec($curl), true);
        $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if (Response::HTTP_OK !== $responseCode) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        try {
            /** @var User $user */
            $user = $userRepository->findOneBy(['name' => $authUser['user_name']]);
            if (empty($user)) {
                $user = new User();
                $user->setName($authUser['user_name']);
                $user->setFullName($authUser['full_name']);
                $user->setRoles(["ROLE_USER"]);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return $user;
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        dd($exception);
        return new Response('Access denied! The service team will be informed about this incident.', Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }
}

