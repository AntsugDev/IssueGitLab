<?php

namespace App\Http\Api\GitLab\Controllers;

use App\Http\Api\GitLab\Collections\BoardColletion;
use App\Http\Controllers\Controller;
use App\Http\Utils\GitLab;
use App\Http\Utils\LoadModel;
use App\Models\GitLab\Boards;
use Symfony\Component\HttpFoundation\Response;

class Board extends Controller
{
    public function index(){
        $load = (new LoadModel(Boards::all()))->getModel();
        return (new BoardColletion($load))->response(request())->setStatusCode(Response::HTTP_OK);
    }

}
