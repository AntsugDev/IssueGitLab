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
use function Illuminate\Tests\Integration\Routing\fail;

class LabelRegisterJob implements ShouldQueue
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

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        try{

            BoardsAndLabels::truncate();
            Labels::truncate();
            Boards::truncate();

            $client = new Client(
                [
                    "base_uri" => env('API_GITLAB'),
                    "headers" => [
                        "Authorization" => "Bearer " . $this->gitLab
                    ]
                ]
            );
            $path = str_replace(':1', $this->project, env('API_GITLAB_LABELS'));
            $request = $client->get($path);
            $response = new Collection(json_decode($request->getBody()->getContents(), true));

            $response->each(function ($item){
                Labels::create([
                    "name" => $item['name'],
                    "description"=> $item['description'],
                    "description_html"=> $item['description_html'],
                    "text_color"=> $item['text_color'],
                    "color"=> $item['color'],
                    "subscribed"=> $item['subscribed'],
                    "priority"=> $item['priority'],
                    "is_project_label"=> $item['is_project_label'],
                ]);
            });

        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }
}
