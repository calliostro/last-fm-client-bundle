<?php

declare(strict_types=1);

use Calliostro\LastFm\LastFmClient;
use Calliostro\LastfmBundle\DependencyInjection\LastFmClientFactory as DiLastFmClientFactory;
use Calliostro\LastfmBundle\Middleware\RateLimiterMiddleware;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // DI Factory for runtime credential validation - unified single method approach
    $services->set('calliostro_lastfm.di_client_factory', DiLastFmClientFactory::class);

    // Main Last.fm API Client - configured in extension using factory
    $services->set('calliostro_lastfm.lastfm_client', LastFmClient::class)
        ->public();

    // Primary alias for autowiring (Symfony 7.4+ and 8.0+)
    // Must be explicitly private to match XML configuration
    $services->alias(LastFmClient::class, 'calliostro_lastfm.lastfm_client')
        ->private();

    // Rate Limiter Middleware - dynamically configured in extension when rate_limiter is set
    // This service definition serves as documentation and provides type hints for IDE
    $services->set('calliostro_lastfm.rate_limiter_middleware', RateLimiterMiddleware::class)
        ->private()
        ->abstract(); // Mark as abstract since it's configured dynamically
};
