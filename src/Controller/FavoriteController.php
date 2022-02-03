<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class FavoriteController extends AbstractController
{
    /**
     * @Route("/favorite", name="favorite")
     * @IsGranted("ROLE_USER")
     */
    public function index(UserRepository $articleRepository): Response
    {
        $articles = $this->getUser()->getFavorites();
        return $this->render('favorite/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
