<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/links")
 */
class LinksController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="links", methods={"GET"})
     */
    public function linksAction(): Response
    {
        $links = $this->getSortedLinks();

        $parameters = ['links' => $links];

        $user = $this->security->getUser();
        if (null !== $user && in_array('ROLE_ADMIN', $user->getRoles())) {
            $form                   = $this->createForm(LinkType::class, null, ['action' => $this->generateUrl('add_link')]);
            $parameters['add_form'] = $form->createView();
        }

        return $this->render('web/links.html.twig', $parameters);
    }

    /**
     * @Route("/", name="add_link", methods={"POST"})
     */
    public function addLinkAction(Request $request): Response
    {
        $link = new Link();
        $form = $this->createForm(LinkType::class, $link);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Link $link */
            $link = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($link);
            $em->flush();
        }

        return $this->redirectToRoute('links');
    }

    /**
     * @Route("/{id}/delete", name="delete_link", methods={"POST"})
     */
    public function deleteLinkAction(int $id): Response
    {
        $doctrine = $this->getDoctrine();

        $link = $doctrine->getRepository(Link::class)->find($id);

        $em = $doctrine->getManager();
        $em->remove($link);
        $em->flush();

        return $this->redirectToRoute('links');
    }

    /**
     * @Route("/{id}", name="change_link_priority", methods={"POST"})
     */
    public function changePriorityAction(int $id, Request $request): Response
    {
        $links = $this->getSortedLinks();

        if ($request->get('down')) {
            $increment = 1;
        } elseif ($request->get('up')) {
            $increment = -1;
        } else {
            return $this->redirectToRoute('links');
        }

        foreach ($links as $key => $link) {
            if ($link->getId() === $id) {
                $links = LinksController::swapLinks($links, $key, $key + $increment);
                break;
            }
        }

        $em = $this->getDoctrine()->getManager();

        $max = count($links);
        foreach ($links as $i => $link) {
            $link->setPriority($max - $i);
            $em->persist($link);
        }

        $em->flush();

        return $this->redirectToRoute('links');
    }

    private function getSortedLinks()
    {
        return $this->getDoctrine()
                    ->getRepository(Link::class)
                    ->findBy([], ['priority' => 'DESC']);
    }

    private static function swapLinks(array $links, int $firstKey, int $secondKey): array
    {
        if (!isset($links[$firstKey]) || !isset($links[$secondKey])) {
            return $links;
        }

        $aux               = $links[$firstKey];
        $links[$firstKey]  = $links[$secondKey];
        $links[$secondKey] = $aux;

        return $links;
    }
}
