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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/contacte")
 */
class ContactController extends AbstractController
{
    private $mailer;
    private $session;
    private $security;
    private $reCaptchaSiteKey;

    public function __construct(
        \Swift_Mailer $mailer,
        SessionInterface $session,
        Security $security,
        string $reCaptchaSiteKey
    ) {
        $this->mailer           = $mailer;
        $this->session          = $session;
        $this->security         = $security;
        $this->reCaptchaSiteKey = $reCaptchaSiteKey;
    }

    /**
     * @Route("/", name="contact", methods={"GET", "POST"})
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
            if ('prod' === $this->getParameter('kernel.environment') && !$this->session->get(ReCaptchaController::VALID_RECAPTCHA, false)) {
                $this->addFlash('danger', 'No s\'ha pogut verificar el ReCaptcha, pots tornar-ho a provar o enviar un email a informacio@jaarribaremclub.com');
            } else {
                $message = $form->getData();

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($message);
                    $em->flush();

                    $this->sendMail($message);

                    $this->addFlash('success', 'Missatge enviat!');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Error a l\'enviar l\'email, torna-ho a provar més tard o comprova els camps del formulari.');
                }
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
        }

        $parameters = [
            'form'               => $form->createView(),
            'recaptcha_site_key' => $this->reCaptchaSiteKey,
        ];

        if ($this->isAdmin()) {
            $parameters['messages'] = $this->getDoctrine()->getRepository(Message::class)->findBy([], ['id' => 'DESC'], 10);
        }

        return $this->render('web/contact.html.twig', $parameters);
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

    private function isAdmin(): bool
    {
        $user = $this->security->getUser();

        return null !== $user && in_array('ROLE_ADMIN', $user->getRoles());
    }
}
