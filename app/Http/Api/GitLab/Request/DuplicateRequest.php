<?php

namespace App\Http\Api\GitLab\Request;

use Illuminate\Foundation\Http\FormRequest;

class DuplicateRequest extends FormRequest
{

    public function rules(){
        return [
            "project_id" => [
                "int", "required"
            ]
        ];
    }

    public function messages()
    {
        return [
            "project_id.required" => "Campo Obbligatorio"
        ];
    }
}
