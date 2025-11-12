<?php

declare(strict_types=1);

use Calliostro\LastFm\LastFmClient;
use Calliostro\LastfmBundle\Factory\LastFmClientFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Factory for creating LastFmClient instances with proper validation
    $services->set('calliostro_lastfm.client_factory', LastFmClientFactory::class);

    // Main Last.fm API Client - configured in extension using factory
    $services->set('calliostro_lastfm.lastfm_client', LastFmClient::class)
        ->public();

    // Primary alias for autowiring
    $services->alias(LastFmClient::class, 'calliostro_lastfm.lastfm_client')
        ->private();
};
