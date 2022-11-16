<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Http\Requests\Api\PricePredictionController\CheckRequest;
use App\Interfaces\PricePrediction\IPricePredictionService;

class PricePredictionController extends Controller
{
    use HttpResponse;

    /**
     * @var $pricePredictionService
     */
    private $pricePredictionService;

    public function __construct(IPricePredictionService $pricePredictionService)
    {
        $this->pricePredictionService = $pricePredictionService;
    }

    /**
     * @param CheckRequest $request
     */
    public function check(CheckRequest $request)
    {
        $response = $this->pricePredictionService->getByParameters(
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

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }
}
