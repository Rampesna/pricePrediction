<?php

namespace App\Services\Eloquent;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\ICarBrandService;
use App\Models\Eloquent\CarBrand;

class CarBrandService implements ICarBrandService
{
    /**
     * @return ServiceResponse
     */
    public function getAll(): ServiceResponse
    {
        return new ServiceResponse(
            true,
            'All car brands',
            200,
            CarBrand::all()
        );
    }

    /**
     * @param int $id
     *
     * @return ServiceResponse
     */
    public function getById(
        int $id
    ): ServiceResponse
    {
        $carBrand = CarBrand::find($id);
        if ($carBrand) {
            return new ServiceResponse(
                true,
                'Car brand',
                200,
                $carBrand
            );
        } else {
            return new ServiceResponse(
                false,
                'Car brand not found',
                404,
                null
            );
        }
    }

    /**
     * @param int $id
     *
     * @return ServiceResponse
     */
    public function delete(
        int $id

    ): ServiceResponse
    {
        $carBrand = $this->getById($id);
        if ($carBrand->isSuccess()) {
            return new ServiceResponse(
                true,
                'Car brand deleted successfully',
                200,
                $carBrand->getData()->delete()
            );
        } else {
            return $carBrand;
        }
    }
}
