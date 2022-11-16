<?php

namespace App\Interfaces\Eloquent;

use App\Core\ServiceResponse;

interface ICarBrandModelService extends IEloquentService
{
    /**
     * @param int $carBrandId
     *
     * @return ServiceResponse
     */
    public function getByCarBrandId(
        int $carBrandId
    ): ServiceResponse;
}
