<?php

namespace App\Http\Utils;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{

    protected int|null $nrPage = 1;
    protected int|null $nrSize = 5;

    protected string|null $orderBy=null;

    protected string|null $order='false';

    protected mixed $chunk;

    protected bool $isChunk = true;

    protected int $totalElement = 0;
    protected int $nrTotalPage = 0;




    /**
     * @param mixed $collection
     */
    public function __construct(mixed $collection, ?bool $isChunk = true)
    {
        $this->isChunk = $isChunk;

        parse_str(request()->getQueryString(),$queryString);
        if(count($queryString) > 0 ){
            $this->nrPage = array_key_exists('page',$queryString) ? $queryString['page'] : 0;
            $this->nrSize = array_key_exists('size',$queryString) ? $queryString['size'] : 5;
            $this->orderBy = array_key_exists('orderBy',$queryString) ? $queryString['orderBy'] : null;
            $this->order = array_key_exists('order',$queryString) ? $queryString['order'] : 'false';
        }

        parent::__construct($collection,$collection->count(),$this->nrSize,$this->nrPage);
        if($this->isChunk)
            $this->chunk = $this->items->chunk($this->nrSize);
        else{
            if(request()->headers->has('X-Total'))
                $this->totalElement = request()->headers->get('X-Total');
            if(request()->headers->has('X-Total-Pages'))
                $this->nrTotalPage = request()->headers->get('X-Total-Pages');
            if(request()->headers->has('X-Page'))
                $this->setCurrentPage(request()->headers->get('X-Page'),"pg. ".request()->headers->get('X-Page'));
            if(request()->headers->has('X-Per-Page'))
                $this->nrSize = request()->headers->get('X-Per-Page');
            $this->orderBy = array_key_exists('order_by',$queryString) ? $queryString['order_by'] : null;
            $this->order = array_key_exists('sort',$queryString) ? $queryString['sort'] : 'false';
        }

    }

    /**
     * @throws \Exception
     */
    protected function checkKey(mixed $collection){
        if(!is_null($this->orderBy) ){
            if(is_array($collection)){
                if (!array_key_exists($this->orderBy, $collection))
                    throw new \Exception('Key ' . $this->orderBy . ' not found into collection');
            }else {
                try{
                    $first = $collection->first()->getAttributes();
                    if (!array_key_exists($this->orderBy, $first))
                        throw new \Exception('Key ' . $this->orderBy . ' not found into collection');
                }catch (\Exception|\Error $e){
                    $first = $collection->first();
                    if (!array_key_exists($this->orderBy, $first))
                        throw new \Exception('Key ' . $this->orderBy . ' not found into collection');
                }
            }
            return true;
        }
        return true;
    }


    /**
     * @throws \Exception
     */
    public function response(): JsonResponse
    {
        $content = $this->chunk->has(($this->currentPage()-1)) ? $this->chunk->get(($this->currentPage()-1))->values() : null;
        if(!is_null($content) && !is_null($this->orderBy)){
            if($this->checkKey($content)) {
                if (strcmp($this->order,'true') === 0)
                    $content = $content->sortBy($this->orderBy);
                else
                    $content = $content->sortByDesc($this->orderBy);
            }

        }

        return new JsonResponse(["data" =>[
            'page' => $this->currentPage(),
            'totalPage' => $this->chunk->count(),
            'size' => $this->perPage(),
            'totalElement' => $this->total(),
            'sort' => [
                "orderBy" => $this->orderBy,
                "order" => $this->order
            ],
            'content' =>!is_null($content) ?  $content->values() : []
        ]
        ],200);
    }

    public function reponseGuzzle(){
        return new JsonResponse(["data" =>[
            'page' => $this->currentPage(),
            'totalPage' => $this->nrTotalPage,
            'size' => $this->nrSize,
            'totalElement' => $this->totalElement,
            'sort' => [
                "orderBy" => $this->orderBy,
                "order" => $this->order
            ],
            'content' =>$this->items
        ]
        ],200);
    }

}
