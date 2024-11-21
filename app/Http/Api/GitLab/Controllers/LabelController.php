<?php

namespace App\Http\Api\GitLab\Controllers;

use App\Http\Api\GitLab\Collections\LabelsCollection;
use App\Http\Controllers\Controller;
use App\Http\Utils\GitLab;
use App\Models\GitLab\Labels;
use Symfony\Component\HttpFoundation\Response;

class LabelController extends Controller
{



    public function index(): \Illuminate\Http\JsonResponse
    {
        return (new LabelsCollection(Labels::all()))->response(request())->setStatusCode(Response::HTTP_OK);
    }
}
