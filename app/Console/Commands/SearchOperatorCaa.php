<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SearchOperatorCaa extends Command
{

    const base_uri = "https://accesso.coldiretti.it";
    const username = "admin";
//    const password = "G@cUDruyu8"; //test;
    const password = "PHed_e7uhaWS"; //produzione;

    const realm ="operatori";

    const base_uri_be = "https://pds-api.coldiretti.it/backend/";


    protected  Client $client;

    protected Client $clientBe;

    protected ConsoleOutput $console;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search-op-caa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param Client $client
     */
    public function __construct()
    {
        $this->client = new Client([
            "base_uri" => self::base_uri,
            'headers' => ['Content-Type' => 'application/json'],
            'verify' => false
        ]);

        $this->clientBe = new Client([
            "base_uri" => self::base_uri_be,
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $this->console = new ConsoleOutput();
        parent::__construct();
    }

    protected function message(string $messagge, ?bool $isError = false){
        $data = Carbon::now()->format('Y-m-d H:i:s');
        $this->console->writeln("\n".$data. '- '.($isError ? 'exception'  : '').'- '.$messagge);
    }

    /**
     * @throws GuzzleException
     */
    protected function token(){
        $request = $this->client->post('/auth/realms/master/protocol/openid-connect/token',
            [
                'Accept' => 'application/json',
                'form_params' => [
                    'username' => self::username,
                    'password' => rawurldecode(self::password),
                    'grant_type' => 'password',
                    'client_id' => 'admin-cli',
                    'verify' => false
                ]
            ]);

        $response = $request->getBody()->getContents();
        $stdClass = json_decode($response);
        if(property_exists($stdClass,'access_token'))
            return $stdClass->access_token;
        else
            throw new \Exception("Impossibile estrarre il token");
    }

    protected function totalUser () {
        $request = $this->client->get('/auth/admin/realms/' . self::realm . '/users/count', [
            'headers' => ['Authorization' => 'Bearer ' . $this->token()],
            'Accept' => 'application/json',
            'verify' => false
        ]);

        $response = $request->getBody()->getContents();

        return json_decode($response);
    }

    protected function _user(int $init, ?int $max = 1000){
        $request = $this->client->get('/auth/admin/realms/' . self::realm . '/users', [
            'headers' => ['Authorization' => 'Bearer ' . $this->token()],
            'Accept' => 'application/json',
            'query' => ["first" => $init, 'max' => $max, 'briefRepresentation' => true],
            'verify' => false
        ]);
        $response = $request->getBody()->getContents();
        return  json_decode($response);
    }

    protected function _operator(string $value){
        try {
            $request = $this->clientBe->get('api/operators/email/' . $value, ['Accept' => 'application/json', 'verify' => false]);
            $response = $request->getBody()->getContents();
            $this->message("BE Operator status response: (" . $request->getStatusCode() . ")");
            if (strcmp($request->getStatusCode(), 200) === 0) {
                $data = json_decode($response)->data;
                if (property_exists($data, 'caa_offices') && count($data->caa_offices) > 0)
                    Storage::disk('public')->put('operators/'.$data->email.'.json', json_encode($data->caa_offices,true));
            }
        }   catch (\Exception|ClientException|GuzzleException $e){
            Log::error($e->getMessage());
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $total = $this->totalUser();
        $this->message("Verifico il numero degli utenti trovati: ".$total);
        if($total > 0){
            $bar = new ProgressBar($this->console,$total);
            $bar->start();
            for($i = 0 ; $i < $total; $i = $i +1000){
                $users = $this->_user($i,1000);
                $barBis = new ProgressBar($this->console,1000);
                $barBis->start();
                array_map(function ($value) use(&$barBis){
                    if(property_exists($value,'email')) {
                        $email = $value->email;
                        $this->_operator($email);
                    }
                    $barBis->advance();
                },$users);
                $barBis->finish();
                $bar->advance();
            }
            $bar->finish();
        }
    }
}
