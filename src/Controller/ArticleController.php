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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
            'articles' => $articleRepository->findBy([], ['date' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="new")
     * @IsGranted("ROLE_USER")
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
            $article->setAuthor($this->getUser());
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

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        // Check wether the logged in user is the owner of the article or the admin
        if (!($this->getUser() === $article->getAuthor()) && in_array('ROLE_ADMIN',$this->getUser()->getRoles()) === false) {
            $this->addFlash('danger', 'seul l\'auteur.e de cet article a le droit de le modifier');
            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // verification upload picture
            $picture = $form->get('picture')->getData();
            
            if ($picture !== null) {
                $newFilename = 'photo-' . uniqid() . '.' . $picture->guessExtension();
            
                try {
                    $picture->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
                $article->setPicture($newFilename);
            }
            $article->setDate((new DateTime()));
            $entityManager->flush();

            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

     /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"}, methods={"POST"})
     */

    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Check wether the logged in user is the owner of the article or the admin
        if (!($this->getUser() === $article->getAuthor()) && in_array('ROLE_ADMIN',$this->getUser()->getRoles()) === false) {
            $this->addFlash('danger', 'seul l\'auteur.e de cet article a le droit de le supprimer');
            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
    }
}
