<?php

namespace App\Http\Utils;

use App\Models\User;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class GitLab
{

    public Client $client;

    protected User $user;

    /**
     * @throws \Exception
     */
    public function init(): Client
    {
        try{
            $this->user = request()->user();
            $this->client = new Client([
                "base_uri" => env('API_GITLAB'),
                "headers" => [
                    "Authorization" => "Bearer " . base64_decode($this->user->access_token)
                ]
            ]);
            return $this->client;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }




}
