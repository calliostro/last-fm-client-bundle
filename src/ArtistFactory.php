<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class ArtistFactory
{
    public static function getArtistService(LastFmClient\Client $client): LastFmClient\Service\Artist
    {
        $artistService = new LastFmClient\Service\Artist();
        $artistService->setClient($client);

        return $artistService;
    }
}
