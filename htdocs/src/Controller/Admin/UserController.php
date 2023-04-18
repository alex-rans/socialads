<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user_index')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $name = $request->query->get('name');
        $users = $doctrine->getManager()->getRepository(User::class)->createQueryBuilder('u')
        ->where('u.fullName LIKE :name')
        ->setParameter('name', '%'.$name.'%')
        ->getQuery()
        ->getResult();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'search' => true
        ]);
    }
    #[Route('/admin/user/{id}/grant', name: 'app_admin_user_grantRights')]
    public function grantAdminRights(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $name = $request->query->get('name');
        $entityManeger = $doctrine->getManager();
        $user = $entityManeger->getRepository(User::class)->find($id);
        $roles = $user->getRoles();
        array_push($roles, "ROLE_ADMIN");
        $user->setRoles($roles);
        $entityManeger->persist($user);
        $entityManeger->flush();

        $this->addFlash('success', 'Granted ' . $user->getFullName() . ' admin rights');
        return $this->redirect($_SERVER['HTTP_REFERER']);

    }

    #[Route('/admin/user/{id}/revoke', name: 'app_admin_user_revokeRights')]
    public function revokeAdminRights(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $name = $request->query->get('name');
        $entityManeger = $doctrine->getManager();
        $user = $entityManeger->getRepository(User::class)->find($id);
        $roles = $user->getRoles();
        $key = array_search('ROLE_ADMIN', $roles);
        unset($roles[$key]);
        $user->setRoles($roles);
        $entityManeger->persist($user);
        $entityManeger->flush();

        $this->addFlash('success', 'Revoked ' . $user->getFullName() . ' admin rights');
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
