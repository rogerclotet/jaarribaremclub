<?php

namespace App\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;

class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{
    /**
     * @var array
     */
    private $menu;

    public function __construct(Environment $twig, bool $debug, array $menu)
    {
        parent::__construct($twig, $debug);
        $this->menu = $menu;
    }

    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException  = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        $code = $exception->getStatusCode();

        return new Response($this->twig->render(
            (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            [
                'status_code'    => $code,
                'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception'      => $exception,
                'logger'         => $logger,
                'currentContent' => $currentContent,
                'menu'           => $this->menu,
            ]
        ));
    }

    protected function findTemplate(Request $request, $format, $code, $showException)
    {
        return parent::findTemplate($request, $format, $code, $showException);
    }
}
