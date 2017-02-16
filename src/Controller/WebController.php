<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebController extends AbstractController
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

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

    /**
     * @Route("/contacte", name="contact", methods={"GET", "POST"})
     */
    public function contactAction(Request $request)
    {
        $message = new Message();

        /** @var Form $form */
        $formBuilder = $this
            ->createFormBuilder($message)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('body', TextareaType::class)
            ->add('send', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

                $this->sendMail($message);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error a l\'enviar l\'email, torna-ho a provar més tard o comprova els camps del formulari.');
            }

            // FIXME clear form in a more elegant way
            $message     = new Message();
            $formBuilder = $this
                ->createFormBuilder($message)
                ->add('name', TextType::class)
                ->add('email', EmailType::class)
                ->add('body', TextareaType::class)
                ->add('send', SubmitType::class);
            $form = $formBuilder->getForm();

            $this->addFlash('success', 'Missatge enviat!');
        }

        return $this->render('web/contact.html.twig', [
            'form'     => $form->createView(),
            'messages' => $this->getDoctrine()->getRepository(Message::class)->findAll(),
        ]);
    }

    private function sendMail(Message $message)
    {
        $mailMessage = (new \Swift_Message())
            ->setSubject($message->getName() . ' (formulari de contacte)')
            ->setFrom('informacio@jaarribaremclub.com')
            ->setTo('informacio@jaarribaremclub.com')
            ->addReplyTo($message->getEmail(), $message->getName())
            ->setBody(
                $this->renderView('email/contact.html.twig', [
                    'message' => $message,
                ]),
                'text/html'
            );

        $successful = $this->mailer->send($mailMessage);

        if (!$successful) {
            throw new \Exception('Failed to send mail');
        }
    }
}
