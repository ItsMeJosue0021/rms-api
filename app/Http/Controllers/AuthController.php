<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthResponseResource;
use App\Http\Resources\Auth\MessageResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService){}

    /**
     * Summary of register
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return (new AuthResponseResource($result))->response()->setStatusCode(201);
    }

    /**
     * Summary of login
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $result = $this->authService->login($request->validated(), $request);

        if (! $result['success']) {
            return (new MessageResource($result['message']))->response()->setStatusCode(401);
        }

        return (new AuthResponseResource($result['payload']))->response();
    }

    /**
     * Summary of me
     * @param Request $request
     * @return UserResource
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Summary of logout
     * @param Request $request
     * @return MessageResource
     */
    public function logout(Request $request): MessageResource
    {
        $this->authService->logout($request->user(), $request);

        return new MessageResource('Logged out.');
    }
}
