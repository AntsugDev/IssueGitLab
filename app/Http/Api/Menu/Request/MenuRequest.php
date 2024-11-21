<?php

namespace App\Http\Api\Menu\Request;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{

    public function rules(){

        return [
            "name" => ['required','string'],
            "route_name" => ['nullable','string'],
            "icon" => ['nullable','string'],
            "child" => ['nullable','integer'],
            "is_general" => ['nullable','string'],
        ];
    }

    public static function _general(string $general){
        if(strcmp($general,'sso') !== 0 || strcmp($general,'root') !== 0)
            return false;
        else return true;
    }



    public function messages()
    {
        return [
            "name.required"=> "Nome del menù obbligatorio"
        ];
    }

}
