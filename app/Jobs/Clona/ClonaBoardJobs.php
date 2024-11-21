<?php

namespace App\Jobs\Clona;

use App\Models\GitLab\Boards;
use App\Models\GitLab\Labels;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ClonaBoardJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;


    public int  $tries = 5;

    public int $backoff= 30;

    protected string $gitLab;
    protected int $project;

    protected mixed $collection;
    public function __construct(string $gitLab,int $project)
    {
        $this->gitLab = $gitLab;
        $this->project = $project;
        //todo da rivedere cosa arriva quì
        $this->collection = Cache::get('jobs_labels_'.$this->project);
        //Cache::delete('jobs_labels_'.$this->project);
    }

    protected function getClient (): Client
    {
        return new Client(
            [
                "base_uri" => env('API_GITLAB'),
                "headers" => [
                    "Authorization" => "Bearer " . $this->gitLab
                ]
            ]
        );
    }

    /**
     * @throws \Exception
     */
    protected function created(Client $client, string $name){
        try{
            $path = str_replace(':1', $this->project, env('API_GITLAB_BOARDS'));
            $request = $client->post($path,[
                "json" => [
                    "name" => $name
                ]
            ]);
            if($request->getStatusCode() >=300)
                throw new \Exception("Error insert labels",Response::HTTP_BAD_REQUEST);
            return json_decode($request->getBody()->getContents(),true);
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    protected function update(Client $client, int $id, array $params){
        try{
            $path = str_replace(':2', $id,str_replace(':1', $this->project, env('API_GITLAB_BOARD_UPDATE')));
            $request = $client->put($path,[
                "json" => $params
            ]);
            if($request->getStatusCode() >=300)
                throw new \Exception("Error insert board",Response::HTTP_BAD_REQUEST);
            return json_decode($request->getBody()->getContents(),true);
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    protected function search(string $name){
        try{
            return $this->collection->filter(function ($item) use($name){
                return strcmp($item['name'],$name) === 0;
            });
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    protected function insert(Client $client, int $labelId, int $boardId): void
    {
        try{
            $path = str_replace(':2', $boardId,str_replace(':1', $this->project, env('API_GITLAB_UP_LABELS_BOARD')));
            $request = $client->put($path,[
                "json" => [
                    "label_id" => $labelId
                ]
            ]);
            if($request->getStatusCode() >=300)
                throw new \Exception("Error aggiornamento labels into board",Response::HTTP_BAD_REQUEST);
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        try {
            $client = $this->getClient();
            $boards = Boards::all();
            $boards->each(function (Boards $board) use($client){
                $name   = $board->name;
                $create = $this->created($client,$name);
                $this->update($client,$create->id,array(
                    "hide_backlog_list" =>$board->hide_backlog_list,
                    "hide_closed_list" =>$board->hide_closed_list,
                ));
                $labels = $board->labels();
                $boardId= $create->id;
                $labels->each(function (Labels $label)use($client,$boardId){
                    $nameLabel     = $label->name;
                    $officialLabel = $this->search($nameLabel);
                    $this->insert($client,$officialLabel->id,$boardId);
                });
            });
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }
}
