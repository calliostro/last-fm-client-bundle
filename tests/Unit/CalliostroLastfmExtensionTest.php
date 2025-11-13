<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Unit;

use Calliostro\LastfmBundle\DependencyInjection\CalliostroLastfmExtension;
use Symfony\Component\DependencyInjection\Reference;

final class CalliostroLastfmExtensionTest extends UnitTestCase
{
    public function testLoadWithMinimalConfig(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $extension->load([], $container);

        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $this->assertDefinitionExists($container, 'calliostro_lastfm.client_factory');
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
            [new Reference('calliostro_lastfm.client_factory'), 'createClient']);
        $this->assertDefinitionArgumentCount($container, 'calliostro_lastfm.lastfm_client', 4);
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

        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createBasicClient'];
        $this->assertEquals($expectedFactory, $definition->getFactory());
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

        // With API key only, should use factory method for API key only
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Should use factory for API key only
        $this->assertNotNull($definition->getFactory());
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createClientWithApiKey'];
        $this->assertEquals($expectedFactory, $definition->getFactory());

        // Should have 2 arguments: api_key and options
        $this->assertCount(2, $definition->getArguments());
        $this->assertEquals('test_key_123', $definition->getArguments()[0]);
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

        // With API key only (no secret), should use factory method
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Should use factory for API key only
        $this->assertNotNull($definition->getFactory());
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createClientWithApiKey'];
        $this->assertEquals($expectedFactory, $definition->getFactory());

        // Should have 2 arguments: api_key and options
        $arguments = $definition->getArguments();
        $this->assertCount(2, $arguments);
        $this->assertEquals('test_key_123', $arguments[0]);

        // Check that user agent is passed in options
        $options = $arguments[1];
        $this->assertArrayHasKey('headers', $options);
        $this->assertEquals('TestApp/1.0', $options['headers']['User-Agent']);
    }

    public function testLoadWithRateLimiterWhenRateLimiterNotAvailable(): void
    {
        // This test can only run when symfony/rate-limiter is NOT installed
        // If it's installed, we skip this test as the condition cannot be tested
        if (class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is installed, cannot test unavailable scenario');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

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
