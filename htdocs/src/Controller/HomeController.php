<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Savedpost;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['name' => 'ASC']);

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts
        ]);
    }
    #[Route('/', name: 'save_new_list', methods: ['POST'])]
    public function saveNewList(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = $request->request->get("data");
        $entityManager = $doctrine->getManager();
        $hash = md5(openssl_random_pseudo_bytes(20));
        $post = new Savedpost();
        $post->setHash($hash);
        $post->setData(json_decode($data));
        $post->setName($request->request->get("name"));

        $entityManager->persist($post);
        $entityManager->flush();

        $this->addFlash('success', "Post successfully saved. Copy the url to share it with someone else");

        return $this->redirect('/?key='.$hash);
    }
    #[Route('/get/posts', name: 'get_posts', methods: ['get'])]
    public function getPosts(ManagerRegistry $doctrine): Response
    {
        $postlist = [];
        $entityManager = $doctrine->getManager();
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['name' => 'ASC']);
        foreach ($posts as $post){
//            dd($post);
            array_push($postlist, [
                'id' => $post->getId(),
                'name' => $post->getName(),
                'baseCost' => $post->getBaseCost(),
                'languageCost' => $post->getLanguageCost(),
                'channelCost' => $post->getChannelCost(),
                'rate' => $post->getRate()
            ]);
        }
        return new JsonResponse(['posts' => $postlist]);
    }

    #[Route('/get/{hash}', name: 'get_list', methods: ['get'])]
    public function getList(string $hash, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(Savedpost::class)->findOneBy(['hash' => $hash]);
        return new JsonResponse(['data' => $post->getData(), 'name' => $post->getName()]);
    }
}
