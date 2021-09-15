<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\MesServices\HandleImage;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN',message:'Accès réservé')]
    public function new(Request $request, HandleImage $handleImage): Response
    {
        $user = $this->getUser();

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $imageFile = $form->get('image_upload')->getData();

            if ($imageFile) 
            {
                $handleImage->saveImage($imageFile, $article);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $article->setUser($user);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN',message:'Accès réservé')]
    public function edit(Request $request, Article $article, HandleImage $handleImage): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $vintageImage = $article->getImage();

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $imageFile = $form->get('image_upload')->getData();
            
            if($imageFile)
            {
                $handleImage->editImage($imageFile,$article, $vintageImage);
            }


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN',message:'Accès réservé')]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }
}
