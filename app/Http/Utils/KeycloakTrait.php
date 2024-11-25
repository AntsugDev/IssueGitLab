<?php

namespace App\Http\Utils;

use App\Http\Resources\User\UserResource;
use App\Models\KeycloackUsers;
use App\Models\Role;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

trait KeycloakTrait
{

    use AuthTrait;

    /**
     * @return Client
     */
    public static function client (): Client
    {
        $baseUri= env('RHSSO_URL');
        return new Client([
            'base_uri' => $baseUri,
            'headers' => ['Content-Type' => 'application/json'],
            'verify' => false
        ]);
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public static function getCerts(): mixed
    {
        try{
            $client = self::client();
            $request = $client->get('certs', ['Accept' => 'application/json', 'verify' => false]);
            $response = $request->getBody()->getContents();
            if(strcmp($request->getStatusCode(),200) === 0)
                return json_decode($response);
            throw new \Exception("Impossibile estrare il certificato",$request->getStatusCode());
        }catch (\Exception|GuzzleException|ClientException $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    protected function getToken(string $code, string $redirectUri){
        try{
            $client = self::client();
            $request = $client->post('token', [
                'Accept' => 'application/json',
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code'=>$code,
                    'client_id' => env('RHSSO_CLIENT_ID'),
                    'redirect_uri'=>$redirectUri
                ],
            ]);

            $response = $request->getBody()->getContents();
            if(empty($response)) return null;
            $response = json_decode($response);
            if (is_null($response)) throw new \Exception('Malformed json response',422);
            return $response;
        }catch (\Exception|GuzzleException|ClientException $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function getLogout(): void
    {
        $tentativi   = 0;
        $maxTentativi = 3;
        while ($tentativi < $maxTentativi) {
           CustomLogger::_write("[SSO] -Tentativo nr. $tentativi",'fn-logout');
            try {
                $user = request()->user();
                $token = Cache::get($user->keycloack_id . '_tk');;
                if (is_null($token))
                    CustomLogger::_write("[SSO] -Impossibile procedere con il logout SSO",'fn-logout');
                else {
                    $refresh_token = Cache::get($user->keycloack_id . '__refresh_token');
                    $client = self::client();
                    CustomLogger::_write("[SSO] - Effettuo la chiamata asincrona per la logout sso",'fn-logout');
                    $request = $client->post('logout', [
                        'Accept' => 'application/json',
                        'form_params' => [
                            'refresh_token' => $refresh_token,
                            'headers' => ['Authorization' => 'Bearer ' . $token],
                            'client_id' => env('RHSSO_CLIENT_ID'),
                        ]
                    ]);
                    if (strcmp($request->getStatusCode(), 204) === 0) {
                        Cache::delete($user->keycloack_id . '__refresh_token');
                        Cache::delete($user->keycloack_id . '_tk');
                        Cache::delete($user->keycloack_id.'_sm_auth');
                        Cache::delete($user->id.'_pss_id_refresh');
                        $tentativi = 3;
                    } else {
                        $tentativi++;
                    }
                }
            } catch (\Exception|GuzzleException|ClientException $e) {
                CustomLogger::_write("[SSO]- Catch: " . $e->getMessage(),'fn-logout');
                    $tentativi++;
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function createKeycloack (string $code,string $redirectUri): UserResource
    {

        try{
            $token = $this->getToken($code,$redirectUri);
            CustomLogger::_write($token->access_token,'token-keycloack');
            $jwt = Keycloak::decode($token->access_token,'SSO');
            Cache::put($jwt->sub.'__refresh_token',$token->refresh_token,$token->refresh_expires_in);
            Cache::put($jwt->sub.'_tk',$token->access_token, $token->expires_in);

            $role = Role::getSSO();
            $user = User::updateOrCreate([
                'keycloack_id' => $jwt->sub,
            ], [
                'cod_anagen' => intval($jwt->preferred_username),
                'email' => $jwt->email,
                'name' =>$jwt->name ?? '',
                'password' => trim(strtolower(explode('@',$jwt->email)[0])),
                'role_id' => $role
            ]);
            return $this->create($user);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }

    }

}
