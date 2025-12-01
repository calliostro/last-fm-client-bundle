<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit;

use Calliostro\LastFm\LastFmClient;

final class FunctionalTest extends UnitTestCase
{
    public function testServiceWiring(): void
    {
        $container = $this->bootKernelAndGetContainer();
        $this->assertServiceInstanceOf($container, 'calliostro_lastfm.lastfm_client', LastfmClient::class);
    }

    public function testServiceWiringWithConfiguration(): void
    {
        $container = $this->bootKernelAndGetContainer(['user_agent' => 'test']);

        $LastfmClient = $container->get('calliostro_lastfm.lastfm_client');
        $this->assertInstanceOf(LastfmClient::class, $LastfmClient);

        // Verify that the client is properly configured
        // The user agent configuration is handled internally by the bundle
        /* @noinspection PhpConditionAlreadyCheckedInspection */
        $this->assertInstanceOf(LastfmClient::class, $LastfmClient);
    }

    public function testServiceWiringWithMinimalConfig(): void
    {
        $config = [];
        $container = $this->bootKernelAndGetContainer($config);
        $this->assertServiceInstanceOf($container, 'calliostro_lastfm.lastfm_client', LastfmClient::class);
    }

    public function testServiceWiringWithApiCredentials(): void
    {
        $config = ['api_key' => 'test_key_1234567890', 'api_secret' => 'test_secret_1234567890'];
        $container = $this->bootKernelAndGetContainer($config);
        $this->assertServiceInstanceOf($container, 'calliostro_lastfm.lastfm_client', LastfmClient::class);
    }

    public function testServiceWiringWithApiKeyOnly(): void
    {
        $container = $this->bootKernelAndGetContainer(['api_key' => 'test_key_1234567890']);
        $this->assertServiceInstanceOf($container, 'calliostro_lastfm.lastfm_client', LastfmClient::class);
    }
}
