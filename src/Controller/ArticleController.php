<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/articles", name="articles_")
 */

class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }
    /**
     * @Route("", name="index")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function addArticle(Request $request): Response 
    {
        $article = new Article;
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // verification upload picture
            $picture = $form->get('picture')->getData();
            
            $newFilename = 'photo-' . uniqid() . '.' . $picture->guessExtension();
            //dd($newFilename);
            try {
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
            $article->setPicture($newFilename);
            $article->setDate((new DateTime()));
            $this->entityManagerInterface->persist($article);
            $this->entityManagerInterface->flush($article);
            $this->addFlash('success', 'Votre bon plan a bien été enregistré');
            return $this->redirectToRoute('home');
        }
        
        return $this->renderForm('article/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }

    
}
