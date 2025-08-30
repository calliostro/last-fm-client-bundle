<?php

namespace Calliostro\LastFmClientBundle\Tests;

use LastFmClient\Client;
use LastFmClient\Service\Album;
use LastFmClient\Service\Artist;
use LastFmClient\Service\Auth as AuthService;
use LastFmClient\Service\Track;
use LastFmClient\Service\User;
use PHPUnit\Framework\TestCase;

final class FunctionalTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $kernel = new CalliostroLastFmClientTestingKernel([
            'api_key' => 'test_api_key',
            'secret' => 'test_secret',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $client = $container->get('calliostro_last_fm_client.client');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testAllServicesAreAvailable(): void
    {
        $kernel = new CalliostroLastFmClientTestingKernel([
            'api_key' => 'test_api_key',
            'secret' => 'test_secret',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test all main services
        $this->assertInstanceOf(Client::class, $container->get('calliostro_last_fm_client.client'));
        $this->assertInstanceOf(Album::class, $container->get('calliostro_last_fm_client.album'));
        $this->assertInstanceOf(Artist::class, $container->get('calliostro_last_fm_client.artist'));
        $this->assertInstanceOf(AuthService::class, $container->get('calliostro_last_fm_client.auth_service'));
        $this->assertInstanceOf(Track::class, $container->get('calliostro_last_fm_client.track'));
        $this->assertInstanceOf(User::class, $container->get('calliostro_last_fm_client.user'));
    }

    public function testConfigurationProcessing(): void
    {
        $kernel = new CalliostroLastFmClientTestingKernel([
            'api_key' => 'test_api_key',
            'secret' => 'test_secret',
            'session' => 'test_session',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that services are properly configured
        $auth = $container->get('calliostro_last_fm_client.auth');
        $this->assertNotNull($auth);

        $client = $container->get('calliostro_last_fm_client.client');
        $this->assertInstanceOf(Client::class, $client);
    }
}
