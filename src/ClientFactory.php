<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class ClientFactory
{
    public static function getClient(LastFmClient\Auth $auth): LastFmClient\Client
    {
        $transport = new LastFmClient\Transport\Curl();

        return new LastFmClient\Client($auth, $transport);
    }
}
