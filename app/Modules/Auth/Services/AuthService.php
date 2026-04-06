<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Requests\LoginRequest;
use App\Traits\HasServiceError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use HasServiceError;

    public function register(array $data): ?User
    {
        $this->clearError();

        try {
            $user = User::create($data);
            Auth::login($user);
            return $user;
        } catch (\Throwable $e) {
            Log::error('Ошибка регистрации', ['error' => $e->getMessage()]);
            $this->setError('Не удалось создать аккаунт.');
            return null;
        }
    }

    public function login(LoginRequest $request): bool
    {
        $this->clearError();

        try {
            $request->authenticate();
            $request->session()->regenerate();
            return true;
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Ошибка входа', ['error' => $e->getMessage()]);
            $this->setError('Не удалось выполнить вход.');
            return false;
        }
    }

    public function logout(Request $request): bool
    {
        $this->clearError();

        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return true;
        } catch (\Throwable $e) {
            Log::error('Ошибка выхода', ['error' => $e->getMessage()]);
            $this->setError('Не удалось выполнить выход.');
            return false;
        }
    }
}
