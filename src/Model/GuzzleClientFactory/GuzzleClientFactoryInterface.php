<?php

namespace App\Model\GuzzleClientFactory;

use GuzzleHttp\Client;

interface GuzzleClientFactoryInterface
{
    public function create(array $config = []): Client;
}