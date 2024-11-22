<?php

namespace App\Jobs\Clona;

use App\Models\GitLab\Boards;
use App\Models\GitLab\Labels;
use App\Models\TmpJobs;
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
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;

class ClonaBoardJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;


    public int  $tries = 5;

    public int $backoff= 30;

    protected string $gitLab;
    protected int $project;

    public function __construct(string $gitLab,int $project)
    {
        $this->gitLab = $gitLab;
        $this->project = $project;
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
    protected function search(Client $client, string $name){
        try{
            $path = str_replace(':1', $this->project, env('API_GITLAB_LABELS'));
            $request = $client->get($path);
            if($request->getStatusCode() >=300)
                throw new \Exception("Nessun dato trovato per quel label",Response::HTTP_BAD_REQUEST);
            $collection = json_decode($request->getBody()->getContents(),true);
            return array_values(array_filter($collection,function ($value) use($name){
                return strcmp($value['name'],$name) === 0;
            }));
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }

    protected function insert(Client $client, int $labelId, int $boardId): void
    {
        try{
            $path = str_replace(':2', $boardId,str_replace(':1', $this->project, env('API_GITLAB_UP_LABELS_BOARD')));
            $request = $client->post($path,[
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

    protected function delDevelopment (Client $client): void
    {

        try{
            $path = str_replace(':1', $this->project, env('API_GITLAB_BOARDS'));
            $request = $client->get($path);
            if($request->getStatusCode() >=300)
                throw new \Exception("Error aggiornamento labels into board",Response::HTTP_BAD_REQUEST);
            $response = json_decode($request->getBody()->getContents(),true);
            $filter = array_values(array_filter($response,function ($value){
                return strcmp($value['name'],'Development') === 0;
            }));
            if(count($filter) > 0){
                $pathDelete = str_replace(':2',$filter[0]['id'],str_replace(':1',$this->project,env('API_GITLAB_BOARDS_DELETE')));
                $request = $client->delete($pathDelete);
                if($request->getStatusCode() >=300)
                    throw new \Exception("Impossibile eliminare la board Development",Response::HTTP_FORBIDDEN);
            }

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
            $console = new ConsoleOutput();
            $client = $this->getClient();
            $boards = Boards::all();
            $boards->each(function (Boards $board) use($client,$console){
                $name   = $board->name;
                $console->writeln("Name board da copiare:".$name);
                $create = $this->created($client,$name);
                $boardId= $create['id'];
                $console->writeln("Board creata con id:".$boardId);
                $this->update($client,$boardId,array(
                    "hide_backlog_list" =>$board->hide_backlog_list,
                    "hide_closed_list" =>$board->hide_closed_list,
                ));
                $console->writeln("Board aggiornata");
                $labels = $board->labels();
                $console->writeln("Count labels:".$labels->count());
                $labels->each(function (Labels $label)use($client,$boardId,$console){
                    $nameLabel     = $label->name;
                    $console->writeln("Label name da copiare:".$nameLabel);
                    $officialLabel = $this->search($client,$nameLabel);

                    if(count($officialLabel)=== 0)
                       Log::info("Impossibile recuperare l'id del label per creare la boards");
                    $console->writeln("Label id trovato:".$officialLabel[0]['id']);
                    $this->insert($client,$officialLabel[0]['id'],$boardId);
                });
            });
            $this->delDevelopment($client);
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }
}
