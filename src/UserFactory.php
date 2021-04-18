<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class UserFactory
{
    public static function getUserService(LastFmClient\Client $client): LastFmClient\Service\User
    {
        $userService = new LastFmClient\Service\User();
        $userService->setClient($client);

        return $userService;
    }
}
