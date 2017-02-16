<?php

namespace App\Listener;

use KeenIO\Client\KeenIOClient;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

class RequestMetricsListener
{
    /**
     * @var KeenIOClient
     */
    private $keenIO;

    /**
     * @var string
     */
    private $environment;

    public function __construct(KeenIOClient $keenIO, string $environment)
    {
        $this->keenIO      = $keenIO;
        $this->environment = $environment;
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        $request = $event->getRequest();

        $this->keenIO->addEvent('request', [
            'env'          => $this->environment,
            'path'         => $request->getRequestUri(),
            'status_code'  => $event->getResponse()->getStatusCode(),
            'ip'           => $request->getClientIp(),
            'session'      => $request->getSession()->getId(),
            'keen'         => [
                'addons' => [[
                    'name'  => 'keen:ip_to_geo',
                    'input' => [
                        'ip' => 'ip',
                    ],
                    'output' => 'ip_geo_info',
                 ]],
            ],
        ]);
    }
}
