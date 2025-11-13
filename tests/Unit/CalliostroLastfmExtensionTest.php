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
        $this->assertDefinitionExists($container, 'calliostro_lastfm.di_client_factory');
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

    public function testLoadWithFullConfiguration(): void
    {
        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'user_agent' => 'CustomAgent/1.0',
                'session_key' => 'test_session_key',
                'rate_limiter' => 'app.my_rate_limiter',
            ],
        ];

        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('calliostro_lastfm.lastfm_client'));
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.rate_limiter_middleware'));

        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $arguments = $clientDefinition->getArguments();

        $this->assertEquals('test_key', $arguments[0]);      // api_key
        $this->assertEquals('test_secret', $arguments[1]);   // api_secret
        $this->assertEquals('test_session_key', $arguments[2]); // session_key

        $optionsArray = $arguments[3];
        $this->assertEquals('CustomAgent/1.0', $optionsArray['headers']['User-Agent']);
        $this->assertTrue(isset($optionsArray['handler']));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function testLoadWithRateLimiterWhenComponentNotAvailable(): void
    {
        // This test runs in a separate process where we can mock the class_exists function
        // by using PHP's namespace fallback behavior

        // Create a mock function in our namespace that overrides class_exists
        eval('
            namespace Calliostro\LastfmBundle\DependencyInjection;
            function class_exists($className) {
                if ($className === "Symfony\\\\Component\\\\RateLimiter\\\\RateLimiterFactory") {
                    return false; // Simulate missing component
                }
                return \\class_exists($className);
            }
        ');

        $container = $this->createContainerBuilder();
        $extension = new CalliostroLastfmExtension();

        $config = [
            [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'rate_limiter' => 'my_rate_limiter_service',
            ],
        ];

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('To use the rate_limiter configuration, you must install symfony/rate-limiter. Run: composer require symfony/rate-limiter');

        $extension->load($config, $container);
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
        $expectedFactory = [new Reference('calliostro_lastfm.di_client_factory'), 'createClient'];
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

        // With API key only, should use unified DI factory
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Should use unified DI factory
        $this->assertNotNull($definition->getFactory());
        $expectedFactory = [new Reference('calliostro_lastfm.di_client_factory'), 'createClient'];
        $this->assertEquals($expectedFactory, $definition->getFactory());

        // Should have 4 arguments: api_key, api_secret, session_key, options
        $this->assertCount(4, $definition->getArguments());
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

        // With API key only (no secret), should use unified DI factory
        $this->assertDefinitionExists($container, 'calliostro_lastfm.lastfm_client');
        $definition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Should use unified DI factory
        $this->assertNotNull($definition->getFactory());
        $expectedFactory = [new Reference('calliostro_lastfm.di_client_factory'), 'createClient'];
        $this->assertEquals($expectedFactory, $definition->getFactory());

        // Should have 4 arguments: api_key, api_secret, session_key, options
        $arguments = $definition->getArguments();
        $this->assertCount(4, $arguments);
        $this->assertEquals('test_key_123', $arguments[0]);  // api_key
        $this->assertNull($arguments[1]);  // api_secret (not set)
        $this->assertNull($arguments[2]);  // session_key (not set)

        // Check that user agent is passed in options
        $options = $arguments[3];
        $this->assertArrayHasKey('headers', $options);
        $this->assertEquals('TestApp/1.0', $options['headers']['User-Agent']);
    }

    public function testIsRateLimiterAvailableReturnsTrue(): void
    {
        $extension = new CalliostroLastfmExtension();

        // Use reflection to test the private method
        $reflection = new \ReflectionClass($extension);
        $method = $reflection->getMethod('isRateLimiterAvailable');
        $method->setAccessible(true);

        $result = $method->invoke($extension);

        // Since symfony/rate-limiter is installed, this should return true
        $this->assertTrue($result);
    }

    /**
     * Test the getAlias method to achieve 100% coverage.
     */
    public function testGetAlias(): void
    {
        $extension = new CalliostroLastfmExtension();

        $this->assertEquals('calliostro_lastfm', $extension->getAlias());
    }
}
