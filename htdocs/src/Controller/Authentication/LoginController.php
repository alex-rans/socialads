<?php

namespace App\Controller\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
//    #[Route('/login', name: 'app_authentication_login')]
//    public function index(AuthenticationUtils $authenticationUtils): Response
//    {
//        $error = $authenticationUtils->getLastAuthenticationError();
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('/authentication/login/index.html.twig', [
//            'last_username' => $lastUsername,
//            'error'         => $error,
//        ]);
//    }
}