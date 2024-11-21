<?php

namespace App\Http\Api\Menu\ResourceAndCollection;

use App\Models\MenuModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Types\Relations\Car;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "route_name" => $this->resource->route_name,
            "icon" => $this->resource->icon,
            "is_general" => $this->resource->is_general,
            "created_at" => !is_null($this->resource->created_at) ? Carbon::parse($this->resource->created_at)->format('Y-m-d H:i:s') : null,
            "updated_at" => !is_null($this->resource->updated_at) ? Carbon::parse($this->resource->updated_at)->format('Y-m-d H:i:s') : null,
            "deleted_at" => !is_null($this->resource->deleted_at) ? Carbon::parse($this->resource->deleted_at)->format('Y-m-d H:i:s') : null,
        ];
    }

    public static  function whenChild(int $id){
        return  MenuModel::where('child',$id)->count();
    }

    public static function query(int $id){
        return MenuModel::where('child',$id)->get();
    }

    protected function child(): array
    {
        $child = [];
        if($this->whenChild($this->resource->id)) {
            $list =  $this->query($this->resource->id);
            $list->each(function ($item) use(&$child){
                if($this->whenChild($item->id))
                    $this->childChild( $this->query($item->id));
                else $child[] = (new MenuCollection($list))->toArray(\request());
            });
        }
        return $child;
    }

    protected function childChild(int $id){

    }
}
