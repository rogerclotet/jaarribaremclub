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
        $links = $this->getDoctrine()
            ->getRepository(Link::class)
            ->findBy([], ['title' => 'ASC']);

        $parameters = ['links' => $links];

        $user = $this->security->getUser();
        if (null !== $user && in_array('ROLE_ADMIN', $user->getRoles())) {
            $form = $this->createForm(LinkType::class, null, ['action' => $this->generateUrl('add_link')]);
            if ($form->isSubmitted() && $form->isValid()) {
                // Handle submission
            }

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
}
