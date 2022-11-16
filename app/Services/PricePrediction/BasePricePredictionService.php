<?php

namespace App\Services\PricePrediction;

use GuzzleHttp\Client;

abstract class BasePricePredictionService
{
    /**
     * @var $client
     */
    protected $client;

    /**
     * @var $mobileDeUrl
     */
    protected $mobileDeUrl;

    /**
     * @var $autoScoutUrl
     */
    protected $autoScoutUrl;

    public function __construct()
    {
        $this->client = new Client;
        $this->mobileDeUrl = env('MOBILEDE_URL');
        $this->autoScoutUrl = env('AUTOSCOUT_URL');
    }
}
