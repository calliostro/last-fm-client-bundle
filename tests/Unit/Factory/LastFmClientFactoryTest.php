<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit\Factory;

use Calliostro\LastFm\LastFmClient;
use Calliostro\LastfmBundle\Factory\LastFmClientFactory;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for LastFmClientFactory.
 *
 * Tests factory pattern with proper runtime validation and clear error messages.
 */
class LastFmClientFactoryTest extends TestCase
{
    private LastFmClientFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new LastFmClientFactory();
    }

    public function testCreateClientWithValidCredentials(): void
    {
        $client = $this->factory->createClient('test_api_key', 'test_api_secret');

        $this->assertInstanceOf(LastFmClient::class, $client);
    }

    public function testCreateClientWithValidCredentialsAndOptions(): void
    {
        $options = [
            'headers' => ['User-Agent' => 'TestApp/1.0'],
            'timeout' => 30,
        ];

        $client = $this->factory->createClient('test_api_key', 'test_api_secret', $options);

        $this->assertInstanceOf(LastFmClient::class, $client);
    }

    public function testCreateClientWithEmptyApiKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last.fm API key is required. Please configure LASTFM_API_KEY environment variable.');

        $this->factory->createClient('', 'test_api_secret');
    }

    public function testCreateClientWithWhitespaceApiKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last.fm API key is required. Please configure LASTFM_API_KEY environment variable.');

        $this->factory->createClient('   ', 'test_api_secret');
    }

    public function testCreateClientWithEmptyApiSecret(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last.fm API secret is required for authenticated operations. Please configure LASTFM_SECRET environment variable.');

        $this->factory->createClient('test_api_key', '');
    }

    public function testCreateClientWithWhitespaceApiSecret(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last.fm API secret is required for authenticated operations. Please configure LASTFM_SECRET environment variable.');

        $this->factory->createClient('test_api_key', '   ');
    }

    public function testCreateClientWithApiKeyOnlyValid(): void
    {
        $client = $this->factory->createClientWithApiKey('test_api_key');

        $this->assertInstanceOf(LastFmClient::class, $client);
    }

    public function testCreateClientWithApiKeyOnlyAndOptions(): void
    {
        $options = [
            'headers' => ['User-Agent' => 'TestApp/1.0'],
        ];

        $client = $this->factory->createClientWithApiKey('test_api_key', $options);

        $this->assertInstanceOf(LastFmClient::class, $client);
    }

    public function testCreateClientWithApiKeyOnlyEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last.fm API key is required. Please configure LASTFM_API_KEY environment variable.');

        $this->factory->createClientWithApiKey('');
    }

    public function testCreateClientWithApiKeyOnlyWhitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Last.fm API key is required. Please configure LASTFM_API_KEY environment variable.');

        $this->factory->createClientWithApiKey('   ');
    }

    public function testCreateBasicClient(): void
    {
        $client = $this->factory->createBasicClient();

        $this->assertInstanceOf(LastFmClient::class, $client);
    }

    public function testCreateBasicClientWithOptions(): void
    {
        $options = [
            'headers' => ['User-Agent' => 'TestApp/1.0'],
            'timeout' => 30,
        ];

        $client = $this->factory->createBasicClient($options);

        $this->assertInstanceOf(LastFmClient::class, $client);
    }

    /**
     * Test that error messages guide developers to the proper configuration.
     */
    public function testErrorMessagesContainHelpfulInformation(): void
    {
        try {
            $this->factory->createClient('', 'test_secret');
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('LASTFM_API_KEY', $e->getMessage());
            $this->assertStringContainsString('environment variable', $e->getMessage());
            $this->assertStringContainsString('https://www.last.fm/api/account/create', $e->getMessage());
        }

        try {
            $this->factory->createClient('test_key', '');
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('LASTFM_SECRET', $e->getMessage());
            $this->assertStringContainsString('environment variable', $e->getMessage());
            $this->assertStringContainsString('authenticated operations', $e->getMessage());
        }
    }
}
