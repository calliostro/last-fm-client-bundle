<?php

namespace Calliostro\LastFmClientBundle\Tests;

use Calliostro\LastFm\LastFmApiClient;
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

        $client = $container->get('calliostro_last_fm_client.api_client');
        $this->assertInstanceOf(LastFmApiClient::class, $client);
    }

    public function testApiClientIsAvailable(): void
    {
        $kernel = new CalliostroLastFmClientTestingKernel([
            'api_key' => 'test_api_key',
            'secret' => 'test_secret',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        // Test the main API client
        $this->assertInstanceOf(LastFmApiClient::class, $container->get('calliostro_last_fm_client.api_client'));
    }

    public function testConfigurationProcessing(): void
    {
        $kernel = new CalliostroLastFmClientTestingKernel([
            'api_key' => 'test_api_key',
            'secret' => 'test_secret',
            'session' => 'test_session',
            'http_client_options' => [
                'timeout' => 30,
                'headers' => ['User-Agent' => 'TestApp/1.0']
            ]
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $client = $container->get('calliostro_last_fm_client.api_client');
        $this->assertInstanceOf(LastFmApiClient::class, $client);
    }
}
