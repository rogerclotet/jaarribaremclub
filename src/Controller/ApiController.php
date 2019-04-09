<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/blog", name="api_blog", methods={"GET"})
     */
    public function blogAction(Request $request)
    {
        $page = $request->get('p', 1);
        if ($page < 0) {
            return $this->redirectToRoute('blog');
        }

        $start = ($page - 1) * BlogController::POSTS_PER_PAGE;

        $posts = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->addOrderBy('p.createdAt', 'desc')
            ->addOrderBy('p.id', 'desc')
            ->setFirstResult($start)
            ->setMaxResults(BlogController::POSTS_PER_PAGE)
            ->getQuery()
            ->getResult();

        return $this->render('web/articles.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/history", name="api_history", methods={"GET"})
     */
    public function historyAction(): Response
    {
        $caminades = $this
            ->getDoctrine()
            ->getRepository(Caminada::class)
            ->createQueryBuilder('c')
            ->orderBy('c.year', 'desc')
            ->getQuery()
            ->getResult();

        return new JsonResponse($caminades);
    }
}
