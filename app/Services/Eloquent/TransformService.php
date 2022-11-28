<?php

namespace App\Services\Eloquent;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\ITransformService;
use App\Models\Eloquent\Transform;
use GuzzleHttp\Client;

class TransformService implements ITransformService
{
    /**
     * @param string $relationType
     * @param int $relationId
     * @param string $targetSystem
     *
     * @return ServiceResponse
     */
    public function getTargetValue(
        string $relationType,
        int    $relationId,
        string $targetSystem
    )
    {
        $targetValue = Transform::where('relation_type', $relationType)
            ->where('relation_id', $relationId)
            ->where('target_system', $targetSystem)->first();
        if ($targetValue) {
            return new ServiceResponse(
                true,
                'Target value',
                200,
                $targetValue->target_value
            );
        } else {
            return new ServiceResponse(
                false,
                'Target value not found',
                404,
                null
            );
        }


    //$client = new Client;
    //$response = $client->post('https://wiveda.de/api/login', [
    //    'query' => [
    //        'email' => 'admin@admin.com',
    //        'password' => '123456'
    //    ]
    //]);
//
    //$token = $response->getBody()->getContents()['response'];
//
    //    $client = new Client;
    //    $response = $client->post('http://127.0.0.1:8000/api/pricePrediction/check', [
    //    'headers' => [
    //        "brand"=> 1,
    //        "model"=> 1,
    //        "yearFrom"=> 2000,
    //        "yearTo"=> 2022,
    //        "kilometerFrom" => 2500,
    //        "kilometerTo" => 200000,
    //        "powerFrom" => 50,
    //        "powerTo" => 500,
    //        "bodyType" => 1,
    //        "doors" => 4,
    //        "fuelTypes" => [2],
    //        "gearBoxes" => [1]
    //   ]
    //]);

    }
}
