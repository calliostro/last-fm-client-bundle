<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit;

use Calliostro\LastfmBundle\DependencyInjection\CalliostroLastfmExtension;

final class CalliostroLastfmExtensionTest extends UnitTestCase
{
    public function testLoadWithMinimalConfig(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $extension->load([], $container);

        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
    }

    public function testLoadWithRateLimiter(): void
    {
        if (!class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is not installed');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'rate_limiter' => 'my_rate_limiter_service',
            ],
        ];

        $extension->load($config, $container);

        // Verify the client is configured properly (no middleware services created anymore)
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');

        // The rate limiter should just be passed as option to the client
        // We can't easily test the exact options here since they're passed as constructor arguments
        // But we verified the client definition exists and extension doesn't throw errors
    }

    public function testLoadWithApiKeyAndSecretOnly(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
            ],
        ];

        $extension->load($config, $container);

        $this->assertDefinitionHasFactory($container, 'calliostro_lastfm.lastfm_client',
            ['Calliostro\\LastFm\\LastFmClientFactory', 'createWithApiKey']);
        $this->assertDefinitionArgumentCount($container, 'calliostro_lastfm.lastfm_client', 3);
        $this->assertDefinitionArgumentEquals($container, 'calliostro_lastfm.lastfm_client', 0, 'test_key');
        $this->assertDefinitionArgumentEquals($container, 'calliostro_lastfm.lastfm_client', 1, 'test_secret');
    }

    public function testLoadWithoutRateLimiter(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [[]]; // Empty configuration

        $extension->load($config, $container);

        // When no rate limiter and no API credentials are configured, the basic client should exist
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
    }

    public function testLoadWithApiKeyOnly(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key_123',
            ],
        ];

        $extension->load($config, $container);

        // With API key only, should use normal constructor (not factory) and method call
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Should not use factory for API key only
        $this->assertNull($definition->getFactory());

        // Should have one method call to setApiCredentials
        $methodCalls = $definition->getMethodCalls();
        $this->assertCount(1, $methodCalls);
        $this->assertEquals('setApiCredentials', $methodCalls[0][0]);
        $this->assertEquals(['test_key_123'], $methodCalls[0][1]);
    }

    public function testLoadWithCustomUserAgent(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'user_agent' => 'CustomAgent/1.0',
            ],
        ];

        $extension->load($config, $container);

        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
    }

    public function testLoadWithApiKeyAndUserAgent(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key_123',
                'user_agent' => 'TestApp/1.0',
            ],
        ];

        $extension->load($config, $container);

        // With API key only (no secret), should use normal constructor and method call
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Should not use factory for API key only
        $this->assertNull($definition->getFactory());

        // Should have one method call to setApiCredentials
        $methodCalls = $definition->getMethodCalls();
        $this->assertCount(1, $methodCalls);
        $this->assertEquals('setApiCredentials', $methodCalls[0][0]);
        $this->assertEquals(['test_key_123'], $methodCalls[0][1]);

        // Check that user agent is passed in options
        $arguments = $definition->getArguments();
        $this->assertCount(1, $arguments);
        $options = $arguments[0];
        $this->assertArrayHasKey('headers', $options);
        $this->assertEquals('TestApp/1.0', $options['headers']['User-Agent']);
    }

    public function testLoadWithRateLimiterWhenRateLimiterNotAvailable(): void
    {
        $container = $this->createContainerBuilder();

        // Create a custom extension that returns false for isRateLimiterAvailable
        $extension = new class extends CalliostroLastfmExtension {
            protected function isRateLimiterAvailable(): bool
            {
                return false;
            }
        };

        $config = [
            [
                'rate_limiter' => 'my_rate_limiter_service',
            ],
        ];

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('To use the rate_limiter configuration, you must install symfony/rate-limiter');

        $extension->load($config, $container);
    }
}
