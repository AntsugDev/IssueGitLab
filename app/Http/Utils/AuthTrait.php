<?php

namespace App\Http\Utils;

use App\Http\Api\Menu\ResourceAndCollection\MenuCollection;
use App\Http\Resources\User\KeycloackUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\KeycloackUsers;
use App\Models\MenuModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\RefreshTokenRepository;

trait AuthTrait
{

    protected function create(User $user, bool $isReturn = true): UserResource|array
    {
        if(Cache::has($user->id.'_pss_id_refresh'))
            Cache::delete($user->id.'_pss_id_refresh');

        $oauthAccess = $user->tokens()->get();
        $oauthAccess->each(function ($item) {
            $item->revoked = true;
            $item->save();
        });
        $token = $user->createToken( preg_replace('/\s+/', '_', strtolower($user->name)), ['*']);
        $expiredAt = Carbon::now()->addMinutes(5);
        $token->token->expires_at = $expiredAt;
        $token->token->save();
        $user = (new LoadModel($user))->getModel();
        $resource =  new UserResource($user);
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $expiredAtRefresh = Carbon::now()->addMinutes(80);
        $refreshToken = $refreshTokenRepository->create([
            'id' => \Illuminate\Support\Str::random(40),
            'access_token_id' => $token->token->id,
            'revoked' => false,
            'expires_at' => $expiredAtRefresh,
        ]);
        Cache::put($user->id.'_pss_id_refresh',$refreshToken->id,$expiredAtRefresh->timestamp);

        $jwt = [
            "data-token"=>
                [
                    "access_token"=> $token->accessToken,
                    "refresh_token" =>$refreshToken->id,
                    "expires_at" => $expiredAt->format('Y-m-d H:i:s'),
                ],
        ];

        if(!Cache::has($user->id.'_enter_at'))
            Cache::put($user->id.'_enter_at',Carbon::now());

        $resource->additional($jwt);
        if($isReturn)
            return $resource;
        else
            return $jwt;
    }

}
