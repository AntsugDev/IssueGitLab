<?php

namespace App\Http\Api\GitLab\Collections;

use App\Http\Api\GitLab\Resources\LabelsResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;

class LabelsCollection extends ResourceCollection
{
    public $collects = LabelsResource::class;

    public function toArray(Request $request): array
    {
        return LabelsResource::collection($this->collection)->toArray($request);
    }
}
