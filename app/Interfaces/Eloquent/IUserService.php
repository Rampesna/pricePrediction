<?php

namespace App\Interfaces\Eloquent;

use App\Core\ServiceResponse;

interface IUserService extends IEloquentService
{
    /**
     * @param string $email
     * @param string $password
     */
    public function login(
        string $email,
        string $password
    ): ServiceResponse;
}
