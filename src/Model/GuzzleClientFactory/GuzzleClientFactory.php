<?php

namespace App\Model\GuzzleClientFactory;

use GuzzleHttp\Client;

class GuzzleClientFactory implements GuzzleClientFactoryInterface
{
    public function create(array $config = []): Client
    {
        return new Client($config);
    }
}