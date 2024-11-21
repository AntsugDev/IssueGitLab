<?php

namespace App\Http\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Utils\Paginator;
use App\Models\FailedJobs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FailedController extends Controller
{

    /**
     * @throws \Exception
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return (new FailedJobsCollection(FailedJobs::all()))->response(request())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function destroy(): Response
    {
        try{
            FailedJobs::truncate();
            return (new Response())->setStatusCode(Response::HTTP_CREATED)->send();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),Response::HTTP_FAILED_DEPENDENCY);
        }
    }

    public function show(int $failedJobs): \Illuminate\Http\JsonResponse
    {
        return (new FailedJobsResouce(FailedJobs::find($failedJobs)))->response(request())->setStatusCode(Response::HTTP_OK);
    }
}
