<?php

namespace App\Helpers;

class TokenHelper
{
    // Function Set Option Cookie
    public static function cookieOpts($exp, $domain, $httponly): array
    {
        return [
            'expires'  => $exp,
            'path'     => '/',
            'domain'   => $domain,
            'httponly' => $httponly,
            'secure'   => true,
            'samesite' => 'Strict',
        ];
    }

    public static function accessTokenPayload($domain, $empcode, $role, $issued_at, $expires, $tokenid)
    {
        return $access_token_payload = [
            'iss' => $domain,
            'aud' => $domain,
            'sub' => $empcode,
            'role' => $role,
            'iat' => $issued_at,
            'nbf' => $issued_at,
            'exp' => $expires,
            'jti' => $tokenid,
        ];
    }

    public static function refreshTokenPayload($domain, $empcode, $role, $issued_at, $expires, $tokenid)
    {
        return $refesh_token_payload = [
            'iss' => $domain,
            'aud' => $domain,
            'sub' => $empcode,
            'role' => $role,
            'iat' => $issued_at,
            'nbf' => $issued_at,
            'exp' => $expires,
            'jti' => $tokenid,
        ];
    }
}
