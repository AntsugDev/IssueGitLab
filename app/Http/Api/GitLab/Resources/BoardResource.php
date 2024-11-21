<?php

namespace App\Http\Api\GitLab\Resources;

use App\Http\Api\GitLab\Collections\LabelsCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource->id,
            "name"=> $this->resource->name,
            "lists"=> !is_null($this->resource->lists) ? json_decode($this->resource->lists,true) :null,
            "labels"=> $this->whenLoaded('labels',function ()use($request){
                if(!is_null($this->resource->labels))
                    return (new LabelsCollection($this->resource->labels))->toArray($request);
                return null;
            })
        ];
    }
}
