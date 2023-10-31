<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_post')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: "post_new", methods: ['GET', 'POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Nouveau post créé avec succès');
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/post/edit/{id}", name: "post_edit", requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash("success", "Post modifié avec succès");

            return $this->redirectToRoute("app_post");
        }

        return $this->render("post/edit.html.twig", [
            "form" => $form,
            'post' => $post,
        ]);
    }

    #[Route("/post/delete/{id}", name: "post_delete", requirements: ["id" => "\d+"], methods: ["POST"])]
    public function delete(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Article supprimé avec succès !');
        return $this->redirectToRoute('app_post');
    }

    #[Route("/post/show/{id}", name: "post_show", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function show(Post $post): Response
    {
        return $this->render("post/show.html.twig", [
            "post" => $post,
        ]);
    }
}
