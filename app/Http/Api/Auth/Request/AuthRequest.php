<?php

namespace App\Http\Api\Auth\Request;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email" => ['email','required'],
            "password" => ['string','required']
        ];
    }

    public function messages(): array
    {
        return [
            "email.required" => "Campo obbligatorio",
            "password.required" => "Campo obbligatorio",
            "email.email" => "Email non valida"
        ];
    }
}
