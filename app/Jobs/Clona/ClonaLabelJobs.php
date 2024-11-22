<?php

namespace App\Jobs\Clona;

use App\Models\GitLab\Labels;
use Carbon\Carbon;
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

class ClonaLabelJobs implements ShouldQueue
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

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        try{
            $labels = Labels::all();
            $client = $this->getClient();
            $labels->each(function (Labels $labels) use($client,&$useMemo){
                $path = str_replace(':1', $this->project, env('API_GITLAB_LABELS'));
                $payload = $labels->toArray();
                unset($payload['id']);
                $request = $client->post($path,[
                    "json" => $payload
                ]);
                if($request->getStatusCode() >=300 && strcmp($request->getStatusCode(),409) !== 0)
                    throw new \Exception("Error insert labels",Response::HTTP_BAD_REQUEST);
                else if(strcmp($request->getStatusCode(),409) === 0 && TmpJobs::where('value',$payload['name'])->count() === 0){
                   //todoi
                }
                $response = json_decode($request->getBody()->getContents(),true);
            });
        }catch (\Exception|GuzzleException|ClientException|ServerException $e){
            throw new \Exception($e->getMessage());
        }
    }
}
