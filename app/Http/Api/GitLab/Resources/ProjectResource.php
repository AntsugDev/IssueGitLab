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

    protected function _getLabels():array
    {
        $gitLab = new GitLab();
        $client = $gitLab->init();
        $list = $client->get('projects/'.$this->resource['id'].'/labels');
        return json_decode($list->getBody()->getContents(),true);
    }

    protected function _getBoards():array
    {
        $gitLab = new GitLab();
        $client = $gitLab->init();
        $list = $client->get('projects/'.$this->resource['id'].'/boards');
        return json_decode($list->getBody()->getContents(),true);
    }
}
