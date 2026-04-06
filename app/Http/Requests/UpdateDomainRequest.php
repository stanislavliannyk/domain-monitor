<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('domain'));
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
}
