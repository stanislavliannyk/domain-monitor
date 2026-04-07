<?php

namespace App\Modules\Domain\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $url = $this->input('url');
            if (! $url) {
                return;
            }

            $host = parse_url($url, PHP_URL_HOST);
            if (! $host) {
                return;
            }

            // Resolve hostname to IP (blocks SSRF via DNS rebinding)
            $ip = gethostbyname($host);

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                $v->errors()->add('url', 'URL должен указывать на публичный хост, а не на внутренний адрес.');
            }
        });
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:100'],
            'url'                => ['required', 'url:http,https', 'max:2048'],
            'check_interval'     => ['required', 'integer', 'min:1', 'max:1440'],
            'request_timeout'    => ['required', 'integer', 'min:1', 'max:60'],
            'check_method'       => ['required', Rule::in(['GET', 'HEAD'])],
            'is_active'          => ['boolean'],
            'notify_on_failure'  => ['boolean'],
            'notification_email' => ['nullable', 'email', 'max:255', 'required_if:notify_on_failure,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'Поле «Название» обязательно.',
            'url.required'               => 'Поле «URL» обязательно.',
            'url.url'                    => 'Введите корректный URL, включая http:// или https://.',
            'check_interval.required'    => 'Укажите интервал проверок.',
            'check_interval.min'         => 'Интервал должен быть не менее 1 минуты.',
            'check_interval.max'         => 'Интервал не может превышать 1440 минут (24 часа).',
            'request_timeout.required'   => 'Укажите таймаут запроса.',
            'check_method.required'      => 'Выберите HTTP-метод.',
            'check_method.in'            => 'HTTP-метод должен быть GET или HEAD.',
            'notification_email.required_if' => 'Укажите email для уведомлений, если они включены.',
            'notification_email.email'   => 'Введите корректный email для уведомлений.',
        ];
    }
}
