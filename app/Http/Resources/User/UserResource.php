<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->resource->id,
            "keycloack_id" => $this->resource->keycloack_id,
            "name" => $this->resource->name,
            "email" => $this->resource->email,
            "cod_anagen" => $this->resource->cod_anagen,
            "roles" => $this->whenLoaded('roles', function () use($request){
                return (new RoleResource($this->resource->roles))->toArray($request);
            }),
            "access_token" => $this->resource->access_token,
            "created_at" => $this->resource->created_at,
            "updated_at" => $this->resource->updated_at,
        ];
    }
}
