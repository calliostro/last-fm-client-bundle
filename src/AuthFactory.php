<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class AuthFactory
{
    public static function getAuth(?string $apiKey, ?string $secret, ?string $token, ?string $session): LastFmClient\Auth
    {
        $auth = new LastFmClient\Auth();
        $auth->setApiKey($apiKey);
        $auth->setSecret($secret);
        $auth->setToken($token);
        $auth->setSession($session);

        return $auth;
    }
}
