<?php

namespace App\Controller\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthLoginFallbackController extends AbstractController
{
    /**
     * This route will never be hit in production because it is handled by the apache module.
     * It's just convenient in development, where the module is missing, to do a redirect here.
     *
     * @Route("/hosted-tools-auth-logout")
     */
    public function indexAction(): Response
    {
        return $this->redirect('/');
    }
}
