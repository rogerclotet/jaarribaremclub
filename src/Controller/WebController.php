<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebController extends AbstractController
{
    /**
     * @Route("/quisom", name="about", methods={"GET"})
     */
    public function aboutAction()
    {
        return $this->render('web/about.html.twig');
    }

    /**
     * @Route("/localitzacio", name="location", methods={"GET"})
     */
    public function locationAction()
    {
        return $this->render('web/location.html.twig');
    }
}
