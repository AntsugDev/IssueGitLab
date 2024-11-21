<?php

namespace App\Http\Api\Auth;

use App\Http\Api\Auth\Request\AuthRequest;
use App\Http\Api\Auth\Request\UserRefreshRequest;
use App\Http\Controllers\Controller;
use App\Http\Utils\AuthTrait;
use App\Http\Utils\CustomLogger;
use App\Http\Utils\KeycloakTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use AuthTrait, KeycloakTrait;

    /**
     * @throws \Exception
     *
     *
     * @OA\OpenApi(
     *     @OA\Info(
     *              title="Lista Api",
     *              version="1.0",
     *              description="Lista Api"
     *     ),
     *
     *  @OA\Tag(
     *                  name="Auth",
     *                  description="Api autenticazione per generare il token e per il refresh token"
     *              ),
     *
     *   @OA\Server(
     *              url="http://localhost:8000/",
     *              description="Ambiente di sviluppo"
     *          ),
     *
     *       )
     *
     *
     * @OA\SecurityScheme(
     *             securityScheme="bearerAuth",
     *             type="http",
     *             scheme="bearer",
     *             bearerFormat="JWT"
     *         )
     *  ################################
     *
     * @OA\Post (
     *       path="/api/auth",
     *              summary="Login",
     *              description="Login",
     *              tags={"Auth"},
     *       @OA\RequestBody(
     *                  required=true,
     *                  description="Dati per la login",
     *       @OA\JsonContent(
     *                      type="object",
     *
     *                          @OA\Property(property="email", type="string"),
     *                          @OA\Property(property="password", type="string"),
     *       ),
     *       ),
     *       @OA\Response(
     *                  response=200,
     *                  description="Ritorna i dati dell'utente e il token necessario per le chiamate",
     *
     *              ),
     *   )
     *
     *
     */
    public function auth(AuthRequest $request){
        if($request->validationData()){
            if(Auth::attempt(["email" => $request->input('email'),'password' => $request->input('password')])){
                $user = Auth::user();
                return $this->create($user)->response($request)->setStatusCode(200);
            }else
                throw new \Exception("User not found",Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * @throws \Exception
     * @OA\Get (
     *        path="/api/at/refresh",
     *               summary="Refresh token",
     *               description="Refresh token",
     *               tags={"Auth"},
     *      security={{"bearerAuth":{}}},
     *
     *        @OA\Response(
     *                   response=200,
     *                   description="Ritorna i dati dell'utente e il token aggiornato necessario per le chiamate",
     *
     *               ),
     *    )
     */
    public function refresh(): \Illuminate\Http\JsonResponse
    {
        $user = request()->user();
        if(!Cache::has($user->id.'_pss_id_refresh'))
            throw new \Exception("Impossibile aggiornare il token",401);

        $refreshId = Cache::get($user->id.'_pss_id_refresh');
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshToken = $refreshTokenRepository->find($refreshId);
        if (!$refreshToken) {
            throw  new \Exception('Invalid refresh token', 401);
        }
        if ($refreshToken->revoked || $refreshToken->expires_at < \Illuminate\Support\Carbon::now()) {
            throw  new \Exception('Refresh token expired', 401);
        }
        $refreshTokenRepository->revokeRefreshToken($refreshToken->id);
        $response = $this->create(request()->user(),false);
        return new JsonResponse($response,200);

    }

    /**
     * @throws \Exception
     * @OA\Get (
     *       path="/api/at/logout",
     *               summary="Logout",
     *               description="Logout",
     *               tags={"Auth"},
     *       security={{"bearerAuth":{}}},
     *  @OA\Response(
     *                    response=200,
     *                    description="Ritorna status code 200 se la logout è anadata a buon fine",
     *
     *                ),
     *   )
     */
    public function logout (): Response
    {
        try{
            $user = \request()->user();
            $role = $user->roles()->get()->pluck('name')->toArray();
            Cache::delete($user->id.'_enter_at');
            if(count($role) > 0 && strcmp($role[0],'sso') === 0) {
                $this->getLogout();
            }

            $oauthAccess = $user->tokens()->get();
            $oauthAccess->each(function ($item) {
                $item->revoked = true;
                $item->save();
                $refresh =Passport::refreshToken()->accessToken()->get();
                $refresh->map(function ($value) {
                    $value->revoked = true;
                    $value->save();
                });
            });


            return (new Response())->setStatusCode(201)->send();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),500);
        }
    }

}
