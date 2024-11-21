<?php

namespace App\Http\Utils;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use phpseclib3\Crypt\RSA;
use phpseclib3\Math\BigInteger;

class Keycloak
{

    use KeycloakTrait;
    public static function base64_url_decode($string, $strict = false)
    {
        $b64 = strtr($string, '-_', '+/');
        return base64_decode($b64, $strict);
    }


    public static function getPublicKey($user)
    {
        $cacheKey = $user . '-public-key';
        $publicKey = Cache::get($cacheKey);
        if ( $publicKey !== null) {
            return $publicKey;
        } else {
            $certs = self::getCerts();
            $cert = $certs->keys[0];
            $key = RSA::loadPublicKey([
                'e' => new BigInteger(self::base64_url_decode($cert->e), 256),
                'n' => new BigInteger(self::base64_url_decode($cert->n), 256)
            ]);
            $publicKey = $key->toString('PKCS8');
            Cache::put($cacheKey, $publicKey, now()->addDays(10));
            return $publicKey;
        }
    }



    /**
     * @throws \Exception
     */
    public static function decode (string $jwt, mixed $user): \stdClass
    {
        try{
            return JWT::decode($jwt, new Key(self::getPublicKey($user), 'RS256'));
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(),502);
        }
    }
}
