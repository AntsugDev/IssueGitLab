<?php

namespace App\Http\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Http\Utils\Keycloak;
use App\Http\Utils\KeycloakTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class KeycloakController extends Controller
{

    use KeycloakTrait;

    /**
     * @return JsonResponse
     * @OA\Get (
     *        path="/api/redirect",
     *                summary="Dati redirect accesso con Keycloack",
     *                description="Dati redirect accesso con Keycloack",
     *                tags={"Auth"},
     *   @OA\Response(
     *                     response=200,
     *                     description="Ritorna status code 200 se la logout è anadata a buon fine",
     *
     *                 ),
     *    )
     */
    public function index(): JsonResponse
    {
        /**
         *
         * todo ### verifica realm
        $realm = "portale";
        parse_str(request()->getQueryString(),$query);
        if(count($query) > 0 && array_key_exists('realm',$query))
            $realm = $query['realm'];
         *
         * todo ### sotto va impostato l'if per cambiare dati
         * */

        $response = new Collection();
        $response->put('redirect',
            env('RHSSO_URL').'auth?client_id='
            .env('RHSSO_CLIENT_ID').
            '&redirect_uri='.env('RHSSO_REDIRECT').
            '&response_type=code&scope=openid profile email');
        return new JsonResponse($response,200);
    }

    /**
     * @throws \Exception
     * @OA\Get (
     *         path="/api/user",
     *                 summary="Creazione del token di tipo passport per lo user",
     *                 description="Creazione del token di tipo passport per lo user",
     *                 tags={"Auth"},
     *    @OA\Response(
     *                      response=200,
     *                      description="Ritorna status code 200 se la logout è anadata a buon fine",
     *
     *                  ),
     *     )
     */
    public function store(): UserResource
    {
        parse_str(request()->getQueryString(),$query);
        if(count($query) === 0 || (count($query) > 0 &&  !array_key_exists('code',$query) && !array_key_exists('session_state',$query)))
            throw new \Exception("Impossibile estrare i dati necessari all'autenticazione",403);
        return $this->createKeycloack($query['code'],env('RHSSO_REDIRECT'));
    }


}
