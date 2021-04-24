<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class AuthServiceFactory
{
    public static function getAuthService(LastFmClient\Client $client): LastFmClient\Service\Auth
    {
        $authService = new LastFmClient\Service\Auth();
        $authService->setClient($client);

        return $authService;
    }
}
