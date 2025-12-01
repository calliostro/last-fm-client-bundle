<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit\DependencyInjection;

use Calliostro\LastfmBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    private Configuration $configuration;
    private Processor $processor;

    public function testEmptyConfiguration(): void
    {
        $configs = [[]];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertArrayNotHasKey('api_key', $config);
        $this->assertArrayNotHasKey('api_secret', $config);
        $this->assertNull($config['session_key']);
        $this->assertNull($config['user_agent']);
        $this->assertNull($config['rate_limiter']);
    }

    public function testConfigurationWithUserAgent(): void
    {
        $configs = [
            [
                'user_agent' => 'MyApp/1.0',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('MyApp/1.0', $config['user_agent']);
        $this->assertArrayNotHasKey('throttle', $config);
    }

    public function testConfigurationWithApiCredentials(): void
    {
        $configs = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('test_key', $config['api_key']);
        $this->assertEquals('test_secret', $config['api_secret']);
    }

    public function testConfigurationWithSessionKey(): void
    {
        $configs = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'session_key' => 'test_session_key',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('test_key', $config['api_key']);
        $this->assertEquals('test_secret', $config['api_secret']);
        $this->assertEquals('test_session_key', $config['session_key']);
    }

    public function testConfigurationWithApiKeyOnly(): void
    {
        $configs = [
            [
                'api_key' => 'my_api_key_123',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('my_api_key_123', $config['api_key']);
    }

    public function testRateLimiterBasicConfiguration(): void
    {
        $configs = [
            [
                'rate_limiter' => 'my_rate_limiter_service',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('my_rate_limiter_service', $config['rate_limiter']);
        $this->assertArrayNotHasKey('throttle', $config);
    }

    public function testCompleteConfiguration(): void
    {
        $configs = [
            [
                'user_agent' => 'TestApp/2.0',
                'api_key' => 'valid_api_key_12345',
                'api_secret' => 'valid_api_secret_12345',
                'session_key' => 'valid_session_key_12345',
                'rate_limiter' => 'my_rate_limiter',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('TestApp/2.0', $config['user_agent']);
        $this->assertEquals('valid_api_key_12345', $config['api_key']);
        $this->assertEquals('valid_api_secret_12345', $config['api_secret']);
        $this->assertEquals('valid_session_key_12345', $config['session_key']);
        $this->assertEquals('my_rate_limiter', $config['rate_limiter']);
    }

    public function testMultipleConfigurationMerging(): void
    {
        $configs = [
            [
                'user_agent' => 'FirstApp/1.0',
                'api_key' => 'first_key',
                'session_key' => 'first_session',
            ],
            [
                'user_agent' => 'SecondApp/2.0',
                'api_secret' => 'second_secret',
                'rate_limiter' => 'my_rate_limiter',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        // Second config should override first where present
        $this->assertEquals('SecondApp/2.0', $config['user_agent']);
        $this->assertEquals('first_key', $config['api_key']); // From first config
        $this->assertEquals('second_secret', $config['api_secret']); // From second config
        $this->assertEquals('first_session', $config['session_key']); // From first config
        $this->assertEquals('my_rate_limiter', $config['rate_limiter']); // From second config
    }

    public function testRateLimiterConfiguration(): void
    {
        $configs = [
            [
                'rate_limiter' => 'my_rate_limiter_factory',
                'api_key' => 'token123456789', // API key for Last.fm
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('my_rate_limiter_factory', $config['rate_limiter']);
        $this->assertEquals('token123456789', $config['api_key']);
    }

    public function testSessionKeyOnlyConfiguration(): void
    {
        $configs = [
            [
                'session_key' => 'session_key_only_123',
            ],
        ];

        $config = $this->processor->processConfiguration($this->configuration, $configs);

        $this->assertEquals('session_key_only_123', $config['session_key']);
        $this->assertArrayNotHasKey('api_key', $config);
        $this->assertArrayNotHasKey('api_secret', $config);
    }

    public function testTreeBuilderReturnsCorrectRootName(): void
    {
        $treeBuilder = $this->configuration->getConfigTreeBuilder();

        $this->assertEquals('calliostro_lastfm', $treeBuilder->buildTree()->getName());
    }

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }
}
