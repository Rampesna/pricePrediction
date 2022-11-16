<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Http\Requests\Api\CarBrandController\GetAllRequest;
use App\Interfaces\Eloquent\ICarBrandService;

class CarBrandController extends Controller
{
    use HttpResponse;

    /**
     * @var $carBrandService
     */
    private $carBrandService;

    public function __construct(ICarBrandService $carBrandService)
    {
        $this->carBrandService = $carBrandService;
    }

    /**
     * @param GetAllRequest $request
     */
    public function getAll(GetAllRequest $request)
    {
        $response = $this->carBrandService->getAll();

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }
}
