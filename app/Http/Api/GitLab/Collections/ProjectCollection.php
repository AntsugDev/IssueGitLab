<?php

namespace App\Http\Api\GitLab\Collections;

use App\Http\Api\GitLab\Resources\ProjectResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;

class ProjectCollection extends ResourceCollection
{

    public $collects = ProjectResource::class;

    public function toArray(Request $request): array
    {
        return ProjectResource::collection($this->collection)->toArray($request);
    }

}
