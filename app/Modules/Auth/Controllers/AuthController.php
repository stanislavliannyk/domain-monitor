<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->service->register($request->validated());

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return $this->success($user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $this->service->login($request);

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return $this->success($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request);

        if ($this->service->getError()) {
            return $this->error($this->service->getError());
        }

        return response()->json(['message' => 'Вы успешно вышли из системы.']);
    }

    public function user(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }
}
