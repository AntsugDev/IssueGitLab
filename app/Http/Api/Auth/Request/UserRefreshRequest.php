<?php

namespace App\Http\Api\Auth\Request;

use Illuminate\Foundation\Http\FormRequest;

class UserRefreshRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "refresh_token" => ["required","string"]
        ];
    }

    public function messages(): array
    {
        return [
            "refresh_token.required" => "Campo obbligatorio"
        ];
    }
}
