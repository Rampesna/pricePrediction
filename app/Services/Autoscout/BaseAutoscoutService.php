<?php

namespace App\Services\Autoscout;

use GuzzleHttp\Client;

abstract class BaseAutoscoutService
{
    /**
     * @var $client
     */
    protected $client;

    /**
     * @var $baseUrl
     */
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client;
        $this->baseUrl = env('AUTOSCOUT_BASE_URL');
    }
}
