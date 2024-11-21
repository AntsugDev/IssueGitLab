<?php

namespace App\Http\Api\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\Seeders\BoardsRegisterJobs;
use App\Jobs\Seeders\LabelRegisterJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpFoundation\Response;

class GitLabController extends Controller
{

    /**
     * @throws \Exception
     */
    public function store(string $accessToken): Response
    {
        try{
            $user = request()->user();
            $user->access_token = base64_encode($accessToken);
            $user->updated_at = Carbon::now();
            $user->save();
            Bus::chain([
                new LabelRegisterJob($accessToken,env('API_GITLAB_PROJECT_BASE_ID')),
                new BoardsRegisterJobs($accessToken,env('API_GITLAB_PROJECT_BASE_ID'))
            ])->dispatch();

            return (new Response())->setStatusCode(Response::HTTP_CREATED)->send();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),Response::HTTP_FORBIDDEN);
        }

    }

}
