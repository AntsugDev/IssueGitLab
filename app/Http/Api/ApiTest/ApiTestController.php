<?php

namespace App\Http\Api\ApiTest;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

class ApiTestController
{

    public function index(): mixed
    {
        $id = null ;
        parse_str(request()->getQueryString(),$query);
        if(count($query) >0 && array_key_exists('id',$query))
            $id = $query['id'];

        $path = 'https://jsonplaceholder.typicode.com/users';
        if(!is_null($id))
            $path .="?id=".$id;

        $client = new Client();
        $response = $client->request('GET',$path);
        return  new JsonResponse(array("data" => json_decode($response->getBody()->getContents(),true)));
    }
}
