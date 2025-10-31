<?php

declare(strict_types=1);

use Calliostro\LastFm\LastFmClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Main Last.fm API Client - configured in extension
    $services->set('calliostro_lastfm.lastfm_client', LastFmClient::class)
        ->public();

    // Primary alias for autowiring
    $services->alias(LastFmClient::class, 'calliostro_lastfm.lastfm_client')
        ->private();
};
