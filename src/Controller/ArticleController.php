<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Form\SearchArticleType;
use App\MesServices\HandleImage;
use App\Repository\ArticleRepository;
use App\Search\SearchArticle;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'article_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN',message:'Accès réservé')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = new SearchArticle();

        $form = $this->createForm(SearchArticleType::class,$search);
        $form->handleRequest($request);


        $articles = $paginator->paginate(
            $articleRepository->findAllArticleByFilter($search),
            $request->query->getInt('page',1),
            5
        );


        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView()
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
    
    #[Route('/{id}', name: 'article_show', methods: ['GET','POST'])]
    public function show(Article $article, Request $request, EntityManagerInterface $em,int $id): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $commentaire->setArticle($article);
            $commentaire->setUser($this->getUser());

            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('article_show',['id' => $id]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView()
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

    #[Route('/delete/article/{id}', name: 'article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN',message:'Accès réservé')]
    public function delete(Request $request, Article $article, HandleImage $handleImage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {

            $vintageImage = $article->getImage();
            $handleImage->deleteImage($vintageImage);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/delete/commentaire/{id}', name: 'commentaire_delete', methods: ['POST'])]
    public function deleteCommentaire(int $id,Request $request,Commentaire $commentaire): Response
    {   
        $article = $commentaire->getArticle();

        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_show',['id' => $article->getId()], Response::HTTP_SEE_OTHER);
    }
}
