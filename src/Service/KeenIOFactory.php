<?php

namespace App\Service;

use KeenIO\Client\KeenIOClient;

class KeenIOFactory
{
    public static function get($config = [])
    {
        if ('true' !== $config['enabled']) {
            return new NoopKeenIOClient();
        }

        return KeenIOClient::factory($config);
    }
}
