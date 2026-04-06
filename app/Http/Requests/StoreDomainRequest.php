<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'url.url'                        => 'Please enter a valid URL including http:// or https://.',
            'notification_email.required_if' => 'Notification email is required when notifications are enabled.',
        ];
    }
}
