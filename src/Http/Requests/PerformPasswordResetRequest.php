<?php

namespace Rainwaves\LaraAuthSuite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerformPasswordResetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
