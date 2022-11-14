<?php

namespace App\Http\Controllers\Api\Autoscout;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Http\Requests\Api\Autoscout\PricePredictionController\CheckRequest;
use App\Interfaces\IAutoscoutService;

class PricePredictionController extends Controller
{
    use HttpResponse;

    /**
     * @var $autoscoutService
     */
    private $autoscoutService;

    public function __construct(IAutoscoutService $autoscoutService)
    {
        $this->autoscoutService = $autoscoutService;
    }

    /**
     * @param CheckRequest $request
     */
    public function check(CheckRequest $request)
    {
        $response = $this->autoscoutService->getByParameters(
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
