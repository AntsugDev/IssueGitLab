<?php

namespace App\Http\Api\GitLab\Resources;

use App\Http\Utils\GitLab;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource['id'],
            "description" => $this->resource['description'],
            "name" => $this->resource['name'],
            "created_at" => $this->resource['created_at'],
            "web_url" => $this->resource['web_url'],
            "issue" => array_key_exists('issues',$this->resource['_links']) ? $this->resource['_links']['issues'] : null,
            "labels" => $this->_getLabels(),
            "boards" => $this->_getBoards(),
        ];
    }

    protected function _getLabels(): bool
    {
        $gitLab = new GitLab();
        $client = $gitLab->init();
        $list = $client->get('projects/'.$this->resource['id'].'/labels');
        $response  = json_decode($list->getBody()->getContents(),true);
        return count($response) >0 ;
    }

    protected function _getBoards(){
        $gitLab = new GitLab();
        $client = $gitLab->init();
        $list = $client->get('projects/'.$this->resource['id'].'/boards');
        $response  = json_decode($list->getBody()->getContents(),true);
        return count(array_filter($response,function ($item){
            return  strcmp(strtoupper($item['name']),'DEVELOPMENT') !== 0;
        })) > 0;
    }
}
