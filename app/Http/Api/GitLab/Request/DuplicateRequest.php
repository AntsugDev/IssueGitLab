<?php

namespace App\Http\Api\GitLab\Request;

use Illuminate\Foundation\Http\FormRequest;

class DuplicateRequest extends FormRequest
{

    public function rules(){
        return [
            "project_id" => [
                "int", "required"
            ],
            "choice" => [
                "array","required"
            ],
            "choice.*" => "in:labels,boards,all"
        ];
    }

    public function messages()
    {
        return [
            "project_id.required" => "Campo Obbligatorio",
            "choice.required" => "Campo obbligatorio",
            "choice.array" => "Il campo deve essere un oggetto",
            "choice.in" => "Il campo può contenere i seguenti valori: labels,boards,all",
        ];
    }
}
