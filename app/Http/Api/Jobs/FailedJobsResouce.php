<?php

namespace App\Http\Api\Jobs;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FailedJobsResouce extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource->id,
            "uuid" => $this->resource->uuid,
            "payload" => $this->resource->payload,
            "exception" => $this->resource->exception,
            "failed_at" => Carbon::parse($this->resource->failed_at)->format('d/m/Y H:i:s'),
        ];
    }
}
