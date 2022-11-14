<?php

namespace App\Services\MobileDe;

use GuzzleHttp\Client;

abstract class BaseMobileDeService
{
    /**
     * @var $client
     */
    protected $client;

    /**
     * @var $baseUrl
     */
    protected $baseUrl;

    /**
     * @var $searchUrl
     */
    protected $searchUrl;

    public function __construct()
    {
        $this->client = new Client;
        $this->searchUrl = env('MOBILEDE_SEARCH_URL');
        $this->baseUrl = env('MOBILEDE_BASE_URL');
    }
}
