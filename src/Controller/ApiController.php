<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Caminada;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/history", methods={"GET"})
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
