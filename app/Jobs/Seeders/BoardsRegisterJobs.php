<?php

namespace App\Jobs\Seeders;

use App\Models\GitLab\Boards;
use App\Models\GitLab\BoardsAndLabels;
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

class BoardsRegisterJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;


    public int  $tries = 5;

    public int $backoff= 30;

    protected string $gitLab;
    protected int $project;
    /**
     * Create a new job instance.
     */
    public function __construct(string $gitLab,int $project)
    {
       $this->gitLab = $gitLab;
       $this->project = $project;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        try{
            $client = new Client(
                [
                    "base_uri" => env('API_GITLAB'),
                    "headers" => [
                        "Authorization" => "Bearer " . $this->gitLab
                    ]
                ]
            );
            $path = str_replace(':1', $this->project, env('API_GITLAB_BOARDS'));
            $request = $client->get($path);
            $response = new Collection(json_decode($request->getBody()->getContents(), true));

            $response->each(function ($item){
                $board = Boards::create([
                    "name" => $item['name'],
                    "hide_backlog_list"=> $item['hide_backlog_list'],
                    "hide_closed_list"  => $item['hide_closed_list'],
                    "lists"=> !is_null($item['lists']) ? json_encode($item['lists'],true): null
                ]);
                if(!is_null($item['lists']) && !is_null($board)){
                    $list = new Collection($item['lists']);
                    $list->each(function ($label) use($board){
                       $labelModel = Labels::where('name',$label['label']['name'])->pluck('id')->toArray();
                       if(count($labelModel) > 0)
                           BoardsAndLabels::create([
                               "id_boards" => $board->id,
                               "id_labels" => $labelModel[0]
                           ]);
                    });
                }
            });

        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }
}
