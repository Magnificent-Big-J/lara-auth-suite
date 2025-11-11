<?php

namespace Rainwaves\LaraAuthSuite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Rainwaves\LaraAuthSuite\Support\Contracts\ProvidesValidationRules;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        $providerClass = config('authx.registration.register_rules_provider');
        if ($providerClass && class_exists($providerClass)) {
            /** @var ProvidesValidationRules $prov */
            $prov = app($providerClass);
            return $prov->for('register');
        }
        return config('authx.registration.rules', []);
    }
}
