<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Integration;

use Calliostro\LastfmBundle\DependencyInjection\CalliostroLastfmExtension;
use Calliostro\LastfmBundle\Tests\Unit\UnitTestCase;
use Symfony\Component\DependencyInjection\Reference;

final class RateLimiterIntegrationTest extends UnitTestCase
{
    public function testRateLimiterConfigurationCreatesMiddleware(): void
    {
        if (!class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is not installed');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        // Create a mock rate limiter factory service
        $container->register('my_rate_limiter_factory', 'Symfony\\Component\\RateLimiter\\RateLimiterFactory');

        $config = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'rate_limiter' => 'my_rate_limiter_factory',
            ],
        ];

        $extension->load($config, $container);

        // Verify the middleware service is created
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_middleware'));

        // Verify the handler stack service is created
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_handler_stack'));

        // Verify the middleware is properly configured
        $middlewareDefinition = $container->getDefinition('calliostro_lastfm.rate_limiter_middleware');
        $this->assertEquals('Calliostro\\LastfmBundle\\Middleware\\RateLimiterMiddleware', $middlewareDefinition->getClass());
        $this->assertEquals([
            new Reference('my_rate_limiter_factory'),
            'lastfm_api',
        ], $middlewareDefinition->getArguments());

        // Verify the handler stack has the middleware pushed
        $handlerStackDefinition = $container->getDefinition('calliostro_lastfm.rate_limiter_handler_stack');
        $this->assertEquals('GuzzleHttp\\HandlerStack', $handlerStackDefinition->getClass());
        $this->assertEquals(['GuzzleHttp\\HandlerStack', 'create'], $handlerStackDefinition->getFactory());

        $methodCalls = $handlerStackDefinition->getMethodCalls();
        $this->assertCount(1, $methodCalls);
        $this->assertEquals('push', $methodCalls[0][0]);
        $this->assertEquals([
            new Reference('calliostro_lastfm.rate_limiter_middleware'),
            'rate_limiter',
        ], $methodCalls[0][1]);

        // Verify the client uses the handler stack
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $clientArguments = $clientDefinition->getArguments();

        // The options array should contain the handler reference
        $this->assertCount(4, $clientArguments); // api_key, api_secret, options, session_key
        $options = $clientArguments[2]; // The options array
        $this->assertArrayHasKey('handler', $options);
        $this->assertEquals(new Reference('calliostro_lastfm.rate_limiter_handler_stack'), $options['handler']);
    }

    public function testRateLimiterConfigurationWithApiKeyOnly(): void
    {
        if (!class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is not installed');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        // Create a mock rate limiter factory service
        $container->register('my_rate_limiter_factory', 'Symfony\\Component\\RateLimiter\\RateLimiterFactory');

        $config = [
            [
                'api_key' => 'test_key_only',
                'rate_limiter' => 'my_rate_limiter_factory',
            ],
        ];

        $extension->load($config, $container);

        // Verify rate limiter services are created
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_middleware'));
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_handler_stack'));

        // Verify the client factory method is correct for API key only
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createClientWithApiKey'];
        $this->assertEquals($expectedFactory, $clientDefinition->getFactory());

        // Verify the client arguments include the handler
        $clientArguments = $clientDefinition->getArguments();
        $this->assertCount(2, $clientArguments); // api_key, options
        $options = $clientArguments[1]; // The options array
        $this->assertArrayHasKey('handler', $options);
        $this->assertEquals(new Reference('calliostro_lastfm.rate_limiter_handler_stack'), $options['handler']);
    }

    public function testRateLimiterConfigurationWithBasicClient(): void
    {
        if (!class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is not installed');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        // Create a mock rate limiter factory service
        $container->register('my_rate_limiter_factory', 'Symfony\\Component\\RateLimiter\\RateLimiterFactory');

        $config = [
            [
                'rate_limiter' => 'my_rate_limiter_factory',
                // No API key provided - should use basic client
            ],
        ];

        $extension->load($config, $container);

        // Verify rate limiter services are created
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_middleware'));
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_handler_stack'));

        // Verify the client factory method is correct for basic client
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createBasicClient'];
        $this->assertEquals($expectedFactory, $clientDefinition->getFactory());

        // Verify the client arguments include the handler
        $clientArguments = $clientDefinition->getArguments();
        $this->assertCount(1, $clientArguments); // options only
        $options = $clientArguments[0]; // The options array
        $this->assertArrayHasKey('handler', $options);
        $this->assertEquals(new Reference('calliostro_lastfm.rate_limiter_handler_stack'), $options['handler']);
    }

    public function testRateLimiterConfigurationWithUserAgent(): void
    {
        if (!class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is not installed');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        // Create a mock rate limiter factory service
        $container->register('my_rate_limiter_factory', 'Symfony\\Component\\RateLimiter\\RateLimiterFactory');

        $config = [
            [
                'api_key' => 'test_key',
                'user_agent' => 'TestApp/1.0',
                'rate_limiter' => 'my_rate_limiter_factory',
            ],
        ];

        $extension->load($config, $container);

        // Verify the client arguments include both handler and user agent
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $clientArguments = $clientDefinition->getArguments();

        $options = $clientArguments[1]; // The options array
        $this->assertArrayHasKey('handler', $options);
        $this->assertArrayHasKey('headers', $options);
        $this->assertEquals('TestApp/1.0', $options['headers']['User-Agent']);
        $this->assertEquals(new Reference('calliostro_lastfm.rate_limiter_handler_stack'), $options['handler']);
    }

    public function testWithoutRateLimiterConfigurationNoMiddlewareCreated(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                // No rate_limiter configured
            ],
        ];

        $extension->load($config, $container);

        // Verify no rate limiter middleware or handler stack services are dynamically created
        // (The abstract service definition in services.php may exist, but not the dynamic ones)
        $this->assertFalse($container->hasDefinition('calliostro_lastfm.rate_limiter_handler_stack'));

        // Verify the client doesn't have a handler option
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $clientArguments = $clientDefinition->getArguments();

        $options = $clientArguments[2]; // The options array
        $this->assertArrayNotHasKey('handler', $options);
    }

    public function testRateLimiterThrowsExceptionWhenComponentNotInstalled(): void
    {
        // Skip this test if symfony/rate-limiter IS installed
        if (class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is installed, cannot test missing component scenario');
        }

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key',
                'rate_limiter' => 'my_rate_limiter_factory',
            ],
        ];

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('To use the rate_limiter configuration, you must install symfony/rate-limiter');

        $extension->load($config, $container);
    }
}
