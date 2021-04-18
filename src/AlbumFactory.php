<?php

namespace Calliostro\LastFmClientBundle;

use LastFmClient;

final class AlbumFactory
{
    public static function getAlbumService(LastFmClient\Client $client): LastFmClient\Service\Album
    {
        $albumService = new LastFmClient\Service\Album();
        $albumService->setClient($client);

        return $albumService;
    }
}
