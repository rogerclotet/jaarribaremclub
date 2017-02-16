<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use KeenIO\Client\KeenIOClient;

class NoopKeenIOClient extends KeenIOClient
{
    public function __construct()
    {
        parent::__construct(new Client(), new Description([]));
    }

    public function __call($method, array $args)
    {
        // Do nothing
    }

    public function addEvent($collection, array $event = [])
    {
        // Do nothing
    }
}
