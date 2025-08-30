<?php

namespace Calliostro\LastFmClientBundle\Tests;

use Calliostro\LastFm\LastFmApiClient;
use Calliostro\LastFm\ClientFactory;
use PHPUnit\Framework\TestCase;

final class ClientFactoryTest extends TestCase
{
    public function testClientFactoryCreatesApiClient(): void
    {
        $client = ClientFactory::create('test_api_key', 'test_secret', []);
        $this->assertInstanceOf(LastFmApiClient::class, $client);
    }

    public function testClientFactoryWithPreConfiguredSession(): void
    {
        $client = ClientFactory::createWithAuth('test_api_key', 'test_secret', 'test_session', []);
        $this->assertInstanceOf(LastFmApiClient::class, $client);
    }

    public function testClientFactoryWithHttpClientOptions(): void
    {
        $client = ClientFactory::create('test_api_key', 'test_secret', [
            'timeout' => 30,
            'headers' => ['User-Agent' => 'TestApp/1.0']
        ]);
        $this->assertInstanceOf(LastFmApiClient::class, $client);
    }
}
