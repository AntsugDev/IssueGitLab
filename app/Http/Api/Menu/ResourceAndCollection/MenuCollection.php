<?php

namespace App\Http\Api\Menu\ResourceAndCollection;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;

class MenuCollection extends ResourceCollection
{

    public $collects = MenuResource::class;

    public $resource = 'menu';

    public function toArray(Request $request): array
    {
        return MenuResource::collection($this->collection)->toArray($request);
    }

}
