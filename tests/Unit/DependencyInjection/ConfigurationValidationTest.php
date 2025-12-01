<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit\DependencyInjection;

use Calliostro\LastfmBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationValidationTest extends TestCase
{
    private Configuration $configuration;
    private Processor $processor;

    public function testEmptyApiKeyNowAllowed(): void
    {
        $configs = [
            [
                'api_key' => '',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals('', $config['api_key']);
    }

    public function testWhitespaceOnlyApiKeyNowAllowed(): void
    {
        $configs = [
            [
                'api_key' => '   ',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals('   ', $config['api_key']);
    }

    public function testShortApiKeyNowAllowed(): void
    {
        $configs = [
            [
                'api_key' => 'short',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals('short', $config['api_key']);
    }

    public function testEmptyApiSecretNowAllowed(): void
    {
        $configs = [
            [
                'api_secret' => '',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals('', $config['api_secret']);
    }

    public function testEmptySessionKeyNowAllowed(): void
    {
        $configs = [
            [
                'session_key' => '',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals('', $config['session_key']);
    }

    public function testTooLongUserAgentFails(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('User-Agent cannot be longer than 200 characters');

        $configs = [
            [
                'user_agent' => str_repeat('A', 201),
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $configs);
    }

    public function testValidConfiguration(): void
    {
        $configs = [
            [
                'api_key' => 'LastFmApiKey2024Token123456789',
                'api_secret' => 'LastFmApiSecret2024Token123456789',
                'session_key' => 'LastFmSessionKey2024Token123456789',
                'user_agent' => 'MyMusicApp/2.0 +https://example.com',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('LastFmApiKey2024Token123456789', $config['api_key']);
        $this->assertEquals('LastFmApiSecret2024Token123456789', $config['api_secret']);
        $this->assertEquals('LastFmSessionKey2024Token123456789', $config['session_key']);
        $this->assertEquals('MyMusicApp/2.0 +https://example.com', $config['user_agent']);
    }

    public function testEnvironmentVariableSyntaxAllowed(): void
    {
        $configs = [
            [
                'api_key' => '%env(LASTFM_API_KEY)%',
                'api_secret' => '%env(LASTFM_API_SECRET)%',
                'session_key' => '%env(LASTFM_SESSION_KEY)%',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals('%env(LASTFM_API_KEY)%', $config['api_key']);
        $this->assertEquals('%env(LASTFM_API_SECRET)%', $config['api_secret']);
        $this->assertEquals('%env(LASTFM_SESSION_KEY)%', $config['session_key']);
    }

    public function testArrayAsScalarValue(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $configs = [
            [
                'api_key' => ['invalid' => 'array'],
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $configs);
    }

    public function testEmptyRateLimiterThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Rate limiter service ID cannot be empty');

        $configs = [
            [
                'rate_limiter' => '',
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $configs);
    }

    public function testComplexUserAgentConfiguration(): void
    {
        $userAgent = 'ComplexApp/3.0 (Linux; Compatible; +https://example.com/about) Mozilla/5.0';

        $configs = [
            [
                'user_agent' => $userAgent,
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);
        $this->assertEquals($userAgent, $config['user_agent']);
    }

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }
}
