<?php

namespace App\Controller;

use KeenIO\Client\KeenIOClient;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recaptcha")
 */
class ReCaptchaController extends AbstractController
{
    public const VALID_RECAPTCHA = 'valid-recaptcha';

    private $reCaptcha;
    private $keenIOClient;
    private $session;

    public function __construct(ReCaptcha $reCaptcha, KeenIOClient $keenIOClient, SessionInterface $session)
    {
        $this->reCaptcha    = $reCaptcha;
        $this->keenIOClient = $keenIOClient;
        $this->session      = $session;
    }

    /**
     * @Route("/verify", name="recaptcha_verify", methods={"POST"})
     */
    public function verifyAction(Request $request)
    {
        $response = $this->reCaptcha->verify($request->get('token') . 'fake');

        $this->keenIOClient->addEvent('recaptcha', ['success' => $response->isSuccess(), 'errors' => $response->getErrorCodes()]);

        $this->session->set(self::VALID_RECAPTCHA, $response->isSuccess());

        if ($response->isSuccess()) {
            return new Response();
        }

        throw new BadRequestHttpException();
    }
}
