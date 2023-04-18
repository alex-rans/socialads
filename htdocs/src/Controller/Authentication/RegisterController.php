<?php

namespace App\Controller\Authentication;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
//    #[Route('/register', name: 'app_authentication_register', methods: ['GET'])]
//    public function index(): Response
//    {
//        return $this->render('authentication/register/index.html.twig', [
//            'controller_name' => 'RegisterController',
//        ]);
//    }
//    #[Route('/register', name: 'app_authentication_registeruser', methods: ['POST'])]
//    public function registerUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, Request $request): Response
//    {
//        $email = $request->get('email');
//        $entityManager = $doctrine->getManager();
//        if($entityManager->getRepository(User::class)->findBy(['email' => $email])){
//            $this->addFlash('success', 'User already exists');
//            return $this->redirect('/register');
//        }
//
//        $user = new User;
//        $plaintextPassword = $request->get('password');
//        // hash the password (based on the security.yaml config for the $user class)
//        $hashedPassword = $passwordHasher->hashPassword(
//            $user,
//            $plaintextPassword
//        );
//        $user->setPassword($hashedPassword);
//        $user->setEmail($email);
//        $entityManager->persist($user);
//        $entityManager->flush();
//
//        $this->addFlash('success', 'You have successfully registered');
//        return $this->redirect('/');
//    }
}
