<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PostController extends AbstractController
{
    private string $regex = '/^(\d\d:\d\d)$/';
    private function decimalHours($time)
    {
        $hms = explode(":", $time);
        return ($hms[0] . ($hms[1]/60));
    }

    #[Route('/admin/post', name: 'app_admin_post_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $decimalToTime = [];
        $entityManager = $doctrine->getManager();
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['name' => 'ASC']);
        foreach ($posts as $post){
            $post->setBaseCostAsTime($post->getBaseCost());
            $post->setLanguageCostAsTime($post->getLanguageCost());
            $post->setChannelCostAsTime($post->getChannelCost());
        }

        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts,
            'baseTime' => $decimalToTime
        ]);
    }

    #[Route('/admin/post/create', name: 'app_admin_post_create')]
    public function Create(ManagerRegistry $doctrine, Request $request): Response{
        $entityManager = $doctrine->getManager();
        $post = new Post();

        //my data types are kinda fucked because im converting from a decimal to a hh:mm to make it easier to read
        //but that means I cant use the object to make my form (as far as im aware) so im doing this hot mess
        //if its stupid but it works its not stupid
        $form = $this->createFormBuilder()
            ->add('Name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ])
                ],
            ])
            ->add('Rate', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ])
                ],
            ])
            ->add('Base_time', TextType::class, [
                'help' => 'Format in 00:00. Example: 01:30 for 1 hour and 30 minutes',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ]),
                    new Regex([
                        'pattern' => $this->regex,
                        'message' => 'Value is not in the right format.'
                    ])
                ]
            ])
            ->add('Extra_language_time', TextType::class, [
                'help' => 'Format in 00:00. Example: 01:30 for 1 hour and 30 minutes',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ]),
                    new Regex([
                        'pattern' => $this->regex,
                        'message' => 'Value is not in the right format.'
                    ])
                ]
            ])
            ->add('Extra_channel_time', TextType::class, [
                'help' => 'Format in 00:00. Example: 01:30 for 1 hour and 30 minutes',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ]),
                    new Regex([
                        'pattern' => $this->regex,
                        'message' => 'Value is not in the right format.'
                    ])
                ]
            ])
            ->add('Save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $post->setName($data['Name']);
            $post->setRate($data['Rate']);
            $post->setBaseCost($this->decimalHours($data['Base_time']));
            $post->setLanguageCost($this->decimalHours($data['Extra_language_time']));
            $post->setChannelCost($this->decimalHours($data['Extra_channel_time']));
            $name = $post->getName();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', "$name has been saved.");
            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render('/admin/post/create.html.twig', ['form' => $form]);
    }

    #[Route('/admin/post/{id}/edit', name: 'app_admin_post_edit')]
    public function Edit(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $post->setBaseCostAsTime($post->getBaseCost());
        $post->setLanguageCostAsTime($post->getLanguageCost());
        $post->setChannelCostAsTime($post->getChannelCost());

        $defaultData = [
            'Name' => $post->getName(),
            'Rate' => $post->getRate(),
            'Base_time' => $post->getBaseCostAsTime(),
            'Extra_language_time' => $post->getLanguageCostAsTime(),
            'Extra_channel_time' => $post->getChannelCostAsTime()
        ];
        $form = $this->createFormBuilder($defaultData)
            ->add('Name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ])
                ],
            ])
            ->add('Rate', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ])
                ],
            ])
            ->add('Base_time', TextType::class, [
                'help' => 'Format in 00:00. Example: 01:30 for 1 hour and 30 minutes',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ]),
                    new Regex([
                        'pattern' => $this->regex,
                        'message' => 'Value is not in the right format.'
                    ])
                ]
            ])
            ->add('Extra_language_time', TextType::class, [
                'help' => 'Format in 00:00. Example: 01:30 for 1 hour and 30 minutes',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ]),
                    new Regex([
                        'pattern' => $this->regex,
                        'message' => 'Value is not in the right format.'
                    ])
                ]
            ])
            ->add('Extra_channel_time', TextType::class, [
                'help' => 'Format in 00:00. Example: 01:30 for 1 hour and 30 minutes',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Value cannot be left blank'
                    ]),
                    new Regex([
                        'pattern' => $this->regex,
                        'message' => 'Value is not in the right format.'
                    ])
                ]
            ])
            ->add('Save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $post->setName($data['Name']);
            $post->setRate($data['Rate']);
            $post->setBaseCost($this->decimalHours($data['Base_time']));
            $post->setLanguageCost($this->decimalHours($data['Extra_language_time']));
            $post->setChannelCost($this->decimalHours($data['Extra_channel_time']));
            $name = $post->getName();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', "$name has been saved.");
            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->renderForm('/admin/post/edit.html.twig', ['form' => $form]);
    }

    #[Route('/admin/post/{id}/delete', name: 'app_admin_post_delete')]
    public function Delete(ManagerRegistry $doctrine, Request $request): Response
    {

        $id = $request->attributes->get('id');
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $name = $post->getName();
        $entityManager->remove($post);
        $entityManager->flush();
        $this->addFlash('success', "Post $name succesfully removed");
        return $this->redirect('/admin/post');
    }
}
