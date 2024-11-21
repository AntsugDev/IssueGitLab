<?php

namespace App\Http\Api\GitLab\Collections;

use App\Http\Api\GitLab\Resources\BoardResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;

class BoardColletion extends ResourceCollection
{
    public $collects = BoardResource::class;

    public function toArray(Request $request): array
    {
        return BoardResource::collection($this->collection)->toArray($request);
    }
}
