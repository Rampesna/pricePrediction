<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Http\Requests\Api\CarBrandModelController\GetAllRequest;
use App\Http\Requests\Api\CarBrandModelController\GetByCarBrandIdRequest;
use App\Interfaces\Eloquent\ICarBrandModelService;

class CarBrandModelController extends Controller
{
    use HttpResponse;

    /**
     * @var $carBrandModelService
     */
    private $carBrandModelService;

    public function __construct(ICarBrandModelService $carBrandModelService)
    {
        $this->carBrandModelService = $carBrandModelService;
    }

    /**
     * @param GetAllRequest $request
     */
    public function getAll(GetAllRequest $request)
    {
        $response = $this->carBrandModelService->getAll();

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }

    /**
     * @param GetByCarBrandIdRequest $request
     */
    public function getByCarBrandId(GetByCarBrandIdRequest $request)
    {
        $response = $this->carBrandModelService->getByCarBrandId(
            $request->carBrandId
        );

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }
}
