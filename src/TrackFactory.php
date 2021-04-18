<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class TrackFactory
{
    public static function getTrackService(LastFmClient\Client $client): LastFmClient\Service\Track
    {
        $trackService = new LastFmClient\Service\Track();
        $trackService->setClient($client);

        return $trackService;
    }
}
