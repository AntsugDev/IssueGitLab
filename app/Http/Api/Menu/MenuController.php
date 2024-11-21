<?php

namespace App\Http\Api\Menu;

use App\Http\Api\Menu\Request\MenuRequest;
use App\Http\Api\Menu\ResourceAndCollection\MenuCollection;
use App\Http\Api\Menu\ResourceAndCollection\MenuResource;
use App\Http\Utils\LoadModel;
use App\Models\MenuModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class MenuController
{

    /**
     * @throws \Exception
     */
    public function index(): \Illuminate\Http\JsonResponse
    {

        $respone = new Collection();
        $user = request()->user();
        $role = $user->roles()->pluck('name')->toArray();
        if(count($role) >0 )
            $filterRole = $role[0];
        $list = MenuModel::all();
        if(!is_null($filterRole))
            $list = MenuModel::where('is_general',$filterRole)->orWhere('is_general','=','All')->get();
        $list->each(function ($item) use(&$respone){
            if(MenuResource::whenChild($item->id)){
                $respone->push($this->loopChild($item,$response));
            }else {
                $respone->push((new MenuResource($item))->toArray(request()));
            }
        });
        return new JsonResponse($respone,200);

    }

    protected function loopChild($item, &$response): void
    {
        $item->each(function ($value) use(&$response){
            $check = MenuResource::whenChild($value->id);
            echo '<li>'.$value->id.' ==== '.$check.'</li>';
//            if(MenuResource::whenChild($value->id)) {
//                $return = $this->loopChild($value, $response);
//                if(!is_null($return))
//                    $response->push($return);
//            }
//            else{
//                $response->push((new MenuResource($value))->toArray(request()));
//            }
        });

    }

    /**
     * @throws \Exception
     */
    public function store(MenuRequest $request){

        if($request->validationData()){
            $data = $request->validationData();
            if(!MenuRequest::_general($data['is_general']))
                throw new \Exception("Il valore di is_general è errato");
            $menu = MenuModel::create($data);
            return (new MenuResource((new LoadModel($menu))->getModel()))->response($request)->setStatusCode(201);
        }

    }


}
