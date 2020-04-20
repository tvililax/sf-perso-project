<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        return $this->render('index/index.html.twig', [
            'posts' => $postRepository->listDate(),
        ]);
    }
}
