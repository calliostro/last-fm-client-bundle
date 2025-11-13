<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit\DependencyInjection;

use Calliostro\LastfmBundle\DependencyInjection\LastFmClientFactory;
use PHPUnit\Framework\TestCase;

final class RuntimeValidationTest extends TestCase
{
    private LastFmClientFactory $factory;

    public function testValidApiKeyAndSecretCreatesClient(): void
    {
        $client = $this->factory->createClient('valid_api_key_123456', 'valid_api_secret_123456', null, []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testValidApiKeyOnlyCreatesClient(): void
    {
        $client = $this->factory->createClient('valid_api_key_123456', null, null, []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testShortApiKeyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('API key must be at least 10 characters long');

        $this->factory->createClient('short', null, null, []);
    }

    public function testShortApiSecretThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('API secret must be at least 10 characters long');

        $this->factory->createClient('valid_api_key_123456', 'short', null, []);
    }

    public function testPartialApiCredentialsThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Incomplete API credentials provided');

        $this->factory->createClient(null, 'api_secret_only', null, []);
    }

    public function testAnonymousClientCreation(): void
    {
        $client = $this->factory->createClient(null, null, null, []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testEmptyStringsTreatedAsNull(): void
    {
        $client = $this->factory->createClient('', '', '', []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testWhitespaceOnlyStringsTreatedAsEmpty(): void
    {
        $client = $this->factory->createClient('   ', '   ', '   ', []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testApiKeyAndSecretWithSessionKey(): void
    {
        $client = $this->factory->createClient('valid_api_key_123456', 'valid_api_secret_123456', 'session_key_123', []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testApiKeyOnlyWithSessionKeyIgnored(): void
    {
        // Session key should be ignored when only API key is provided
        $client = $this->factory->createClient('valid_api_key_123456', null, 'session_key_123', []);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    public function testShortApiKeyWithValidSecretStillFails(): void
    {
        // API key validation should happen first and fail
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('API key must be at least 10 characters long');

        $this->factory->createClient('short', 'valid_api_secret_123456', null, []);
    }

    public function testExceptionContainsHelpfulInstructions(): void
    {
        try {
            $this->factory->createClient('short', null, null, []);
            $this->fail('Expected exception was not thrown');
        } catch (\InvalidArgumentException $e) {
            $message = $e->getMessage();
            $this->assertStringContainsString('API key must be at least 10 characters long', $message);
            $this->assertStringContainsString('To configure Last.fm API credentials:', $message);
            $this->assertStringContainsString('https://www.last.fm/api/account/create', $message);
            $this->assertStringContainsString('%env(LASTFM_API_KEY)%', $message);
        }
    }

    public function testOptionsArePassedThrough(): void
    {
        $options = ['headers' => ['User-Agent' => 'TestApp/1.0']];
        $client = $this->factory->createClient('valid_api_key_123456', null, null, $options);
        $this->assertInstanceOf('Calliostro\\LastFm\\LastFmClient', $client);
    }

    protected function setUp(): void
    {
        $this->factory = new LastFmClientFactory();
    }
}
