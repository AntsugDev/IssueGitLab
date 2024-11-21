<?php

namespace App\Http\Utils;

use App\Http\Utils\CustomLogger;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class LoadModel
{
    protected array $parser;

    protected mixed $model;

    /**
     * @param mixed $model
     */
    public function __construct(mixed $model)
    {
        $this->parser = [];
        $this->model = $model;
    }


    protected  function getParser(): void
    {
        $queryString = request()->getQueryString();
        if(stristr($queryString,'load') !== false){
            parse_str(request()->getQueryString(),$tmp);
            $relationship = explode(',',$tmp['load']);
            for ($i = 0 ; $i < count($relationship); $i ++){
                $this->parser[] = $relationship[$i];
            }
        }
    }

    protected  function loadRelationishp(): void
    {

        try {
            $this->getParser();
            if(count($this->parser) > 0 ) {
                $this->model = $this->model->load($this->parser);
            }
        }catch (NotFoundResourceException $e) {
            throw new NotFoundResourceException($e->getMessage());
        }

    }

    /**
     * @throws \Exception
     */
    public function getModel(): mixed
    {
        try{
            $this->loadRelationishp();
            return $this->model;
        }catch (\Exception|\Error $e){
            throw new \Exception($e->getMessage());
        }
    }

}
