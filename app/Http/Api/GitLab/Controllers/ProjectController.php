<?php

namespace App\Http\Api\GitLab\Controllers;

use App\Http\Api\GitLab\Collections\ProjectCollection;
use App\Http\Controllers\Controller;
use App\Http\Utils\GitLab;
use App\Http\Utils\Paginator;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{

    protected GitLab $gitLab;

    /**
     * @param GitLab $gitLab
     */
    public function __construct(GitLab $gitLab)
    {
        $this->gitLab = $gitLab;
    }


    /**
     * @throws GuzzleException
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $client = $this->gitLab->init();
        parse_str(request()->getQueryString(),$query);
        $request = $client->get('projects',[
            "query" => $query
        ]);
        $headers = $request->getHeaders();
        request()->headers->add($headers);
        $response = json_decode($request->getBody()->getContents(), true);
        return (new Paginator(new ProjectCollection($response),false))->reponseGuzzle();
    }

    public function show(string $search){
        $client = $this->gitLab->init();
        parse_str(request()->getQueryString(),$query);
        $query['search'] = $search;
        $request = $client->get('projects',[
            "query" => $query
        ]);
        $headers = $request->getHeaders();
        request()->headers->add($headers);
        $response = json_decode($request->getBody()->getContents(), true);
        return (new Paginator(new ProjectCollection($response),false))->reponseGuzzle();
    }

    /**
     * @throws \Exception
     */
    public function check(int $id){
        $gitLab = new GitLab();
        $client = $gitLab->init();
        $label = $client->getAsync('projects/'.$id.'/labels');
        $boards = $client->getAsync('projects/'.$id.'/boards');
        $promise = Utils::settle([$label,$boards])->wait();
        $error = [];
        $data = [];
        if(strcmp($promise[0]['state'],'fulfilled')===0){
            $decodeLabel = json_decode($promise[0]['value']->getBody()->getContents(),true);
            $data['isLabels'] = !count($decodeLabel) > 0;
        }else{
            $error[]= $promise[0]['reason'];
        }

        if(strcmp($promise[1]['state'],'fulfilled')===0){
            $decodeBoard = json_decode($promise[1]['value']->getBody()->getContents(),true);
            $data['isBoards'] = !count(array_filter($decodeBoard,function ($item){
                return strcmp(strtoupper($item['name']),'DEVELOPMENT') !== 0;
            })) > 0;
        }else{
            $error[]= $promise[1]['reason'];
        }
        if(count($error) > 0)
            throw new \Exception(implode(',',$error),Response::HTTP_UNPROCESSABLE_ENTITY);
        return new JsonResponse(array(
            "data" => $data
        ),Response::HTTP_OK);
    }

}
