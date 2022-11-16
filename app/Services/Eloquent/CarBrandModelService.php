<?php

namespace App\Services\Eloquent;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\ICarBrandModelService;
use App\Models\Eloquent\CarBrand;
use App\Models\Eloquent\CarBrandModel;

class CarBrandModelService implements ICarBrandModelService
{
    /**
     * @return ServiceResponse
     */
    public function getAll(): ServiceResponse
    {
        return new ServiceResponse(
            true,
            'All car brand models',
            200,
            CarBrandModel::all()
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
        $carBrandModel = CarBrandModel::find($id);
        if ($carBrandModel) {
            return new ServiceResponse(
                true,
                'Car brand model',
                200,
                $carBrandModel
            );
        } else {
            return new ServiceResponse(
                false,
                'Car brand model not found',
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
        $carBrandModel = $this->getById($id);
        if ($carBrandModel->isSuccess()) {
            return new ServiceResponse(
                true,
                'Car brand model deleted',
                200,
                $carBrandModel->getData()->delete()
            );
        } else {
            return $carBrandModel;
        }
    }

    /**
     * @param int $carBrandId
     *
     * @return ServiceResponse
     */
    public function getByCarBrandId(
        int $carBrandId
    ): ServiceResponse
    {
        $carBrand = CarBrand::find($carBrandId);
        if ($carBrand) {
            return new ServiceResponse(
                true,
                'Car brand models',
                200,
                CarBrandModel::where('car_brand_id', $carBrandId)->get()
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
}
