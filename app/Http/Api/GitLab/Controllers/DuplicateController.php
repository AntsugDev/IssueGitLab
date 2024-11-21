<?php

namespace App\Http\Api\GitLab\Controllers;

use App\Http\Api\GitLab\Request\DuplicateRequest;
use App\Http\Controllers\Controller;
use App\Jobs\Child\ClearBoardJobs;
use App\Jobs\Clona\ClonaBoardJobs;
use App\Jobs\Clona\ClonaLabelJobs;
use App\Jobs\GlobalDuplicateJobs;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpFoundation\Response;

class DuplicateController extends Controller
{

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(DuplicateRequest $request){
        if($request->validationData()){
            try{
                $data = $request->validationData();
                $projectId =$data['project_id'];
                $user = $request->user();
                Bus::batch([
                    new ClonaLabelJobs($user->access_token,$projectId),
                    new ClonaBoardJobs($user->access_token,$projectId)
                ])->dispatch();
                return (new Response())->setStatusCode(Response::HTTP_CREATED)->send();
            }catch (\Exception $e){
                throw new \Exception($e->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }

    }




}
