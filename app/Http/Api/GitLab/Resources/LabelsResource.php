<?php

namespace App\Http\Api\GitLab\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabelsResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "description" => $this->resource->description,
            "text_color" => $this->resource->text_color,
            "color" => $this->resource->color,
            "priority" => $this->resource->priority,
        ];
    }

}
