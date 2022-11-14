<?php

namespace App\Http\Controllers\Api\MobileDe;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Core\ServiceResponse;
use App\Http\Requests\Api\MobileDe\PricePredictionController\CheckRequest;
use App\Interfaces\IMobileDeService;
use Facebook\WebDriver\Chrome\ChromeDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class PricePredictionController extends Controller
{
    use HttpResponse;

    /**
     * @var $mobileDeService
     */
    private $mobileDeService;

    public function __construct(IMobileDeService $mobileDeService)
    {
        $this->mobileDeService = $mobileDeService;
    }

    /**
     * @param CheckRequest $request
     */
    public function check(CheckRequest $request)
    {
        set_time_limit(86400);
        $chromeDriver = ChromeDriver::start();
        $chromeDriver->get('https://suchen.mobile.de/fahrzeuge/search.html?dam=0&isSearchRequest=true&ms=3500%3B73%3B%3B&ref=quickSearch&sb=rel&vc=Car');

        return 1;

        $response = $this->mobileDeService->getByParameters(
            $request->brand,
            $request->model,
            $request->kilometerFrom,
            $request->kilometerTo,
            $request->yearFrom,
            $request->yearTo,
            $request->fuelTypes ?? [],
            $request->gearBoxes ?? [],
            $request->powerFrom,
            $request->powerTo
        );

        return $response->getData();

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }
}
