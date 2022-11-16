<?php

namespace App\Http\Controllers\Api;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Http\Requests\Api\UserController\LoginRequest;
use App\Interfaces\Eloquent\IUserService;

class UserController extends Controller
{
    use HttpResponse;

    /**
     * @var $userService
     */
    private $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        $response = $this->userService->login(
            $request->email,
            $request->password
        );

        return $this->httpResponse(
            $response->getMessage(),
            $response->getStatusCode(),
            $response->getData(),
            $response->isSuccess()
        );
    }
}
