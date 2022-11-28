<?php

namespace App\Interfaces\Eloquent;

use App\Core\ServiceResponse;

interface ITransformService
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
    );
}
