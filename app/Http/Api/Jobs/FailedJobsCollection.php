<?php

namespace App\Http\Api\Jobs;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;

class FailedJobsCollection extends ResourceCollection
{
    public $collects = FailedJobsResouce::class;

    public function toArray(Request $request): array
    {
        return FailedJobsResouce::collection($this->collection)->toArray($request);
    }
}
