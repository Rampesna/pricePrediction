<?php

namespace App\Services\Eloquent;

use App\Core\ServiceResponse;
use App\Interfaces\Eloquent\IUserService;
use App\Models\Eloquent\User;
use Illuminate\Support\Facades\Hash;

class UserService implements IUserService
{
    /**
     * @return ServiceResponse
     */
    public function getAll(): ServiceResponse
    {
        return new ServiceResponse(
            true,
            'all users',
            200,
            User::all()
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
        $user = User::find($id);
        if ($user) {
            return new ServiceResponse(
                true,
                'User',
                404,
                $user
            );
        } else {
            return new ServiceResponse(
                false,
                'User not found',
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
        $user = $this->getById($id);
        if ($user->isSuccess()) {
            return new ServiceResponse(
                true,
                'User deleted',
                200,
                $user->getData()->delete()
            );
        } else {
            return $user;
        }
    }

    /**
     * @param string $email
     * @param string $password
     */
    public function login(
        string $email,
        string $password
    ): ServiceResponse
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return new ServiceResponse(
                false,
                'User not found',
                404,
                null
            );
        } else {
            if (Hash::check($password, $user->password)) {
                return new ServiceResponse(
                    true,
                    'User logged in',
                    200,
                    $user->createToken('authToken')->plainTextToken
                );
            } else {
                return new ServiceResponse(
                    false,
                    'Password is incorrect',
                    401,
                    null
                );
            }
        }
    }
}
