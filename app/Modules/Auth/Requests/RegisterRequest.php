<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Поле имя обязательно.',
            'email.required'     => 'Поле email обязательно.',
            'email.email'        => 'Введите корректный адрес электронной почты.',
            'email.unique'       => 'Пользователь с таким email уже зарегистрирован.',
            'password.required'  => 'Поле пароль обязательно.',
            'password.confirmed' => 'Пароли не совпадают.',
        ];
    }
}
