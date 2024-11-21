<?php

namespace App\Http\Middleware;

use App\Http\Utils\AuthTrait;
use App\Http\Utils\CustomLogger;
use App\Http\Utils\Keycloak;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use Symfony\Component\HttpFoundation\Response;

class ValidateSso extends Keycloak
{
    use AuthTrait;
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = request()->user();
        $response = $next($request);
        if($user instanceof User) {

            if($this->_enterAt($user)) {
                if ($this->_roles($user)) {
                    $sso = Cache::get($user->keycloack_id . '_tk');
                    if (is_null($sso))
                        $this->invalidatePassport($user);
                    else {
                        $decode = $this->decode($sso, $user->id);
                        $exp = isset($decode->exp) ? Carbon::createFromTimestamp($decode->exp) : null;
                        if (is_null($exp))
                            throw new \Exception("Impossibile verificare la validazione delle chiamate", 401);

                        $now = Carbon::now();
                        if ($now->diffInMinutes($exp) > 0)
                            return $response;
                         else
                            $this->invalidatePassport($user);

                    }
                }
            }
        }

        return $response;
    }


    protected function _roles(User $user): bool
    {
        $role = $user->roles()->get()->pluck('name');
        return count($role) > 0 && strcmp($role[0], 'sso') === 0;
    }

    protected function _enterAt (User $user):bool
    {
        if(Cache::has($user->id.'_enter_at')){
            $enterAt = Cache::get($user->id.'_enter_at');
            $now = Carbon::now();
            if($now->diffInMinutes($enterAt) > 80)
                $this->invalidatePassport($user);
            else
                return true;
        }
        return true;
    }


    /**
     * @throws \Exception
     */
    protected function invalidatePassport (User $user): void
    {
        try {
            $accessToken = Passport::token()->where('user_id', $user->id)->first();

            if (!is_null($accessToken)) {
                $accessToken->revoked = 1;
                $accessToken->save();
            }
            $client = Client::where('user_id', $user->id)->first();
            if (!is_null($client)) {
                $repository = app(ClientRepository::class);
                $repository->regenerateSecret($client);
            }

            Cache::delete($user->keycloack_id.'_tk');
            Cache::delete($user->keycloack_id.'_sm_auth');
            Cache::delete($user->keycloack_id.'__refresh_token');
            Cache::delete($user->id.'_enter_at');

            throw new \Exception("Non autorizzato",401);
        }catch (\Exception $e){
            throw new \Exception("Impossibile rigenerare il secret code per l'utente",401);
        }
    }
}
