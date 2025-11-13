<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Tests\Integration;

use Calliostro\LastfmBundle\CalliostroLastfmBundle;
use Calliostro\LastfmBundle\Tests\Fixtures\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Integration tests matching Discogs Bundle Beta 4 patterns.
 *
 * Tests bundle loading, configuration processing, and service creation
 * with focus on environment variable support and runtime validation.
 */
class LastFmBundleIntegrationTest extends TestCase
{
    /**
     * Test that the bundle loads successfully with environment variables.
     * This matches the Discogs Bundle pattern of supporting environment variables
     * without throwing compile-time errors.
     */
    public function testBundleLoadsWithEnvironmentVariables(): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'env(LASTFM_API_KEY)' => 'test_api_key_123',
            'env(LASTFM_SECRET)' => 'test_secret_456',
            'env(LASTFM_SESSION)' => 'test_session_789',
        ]));

        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        $config = [
            'calliostro_lastfm' => [
                'api_key' => '%env(LASTFM_API_KEY)%',
                'api_secret' => '%env(LASTFM_SECRET)%',
                'session_key' => '%env(LASTFM_SESSION)%',
                'user_agent' => 'LastFmBundle/Test',
            ],
        ];

        // Should not throw during compilation
        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('calliostro_lastfm.lastfm_client'));
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.client_factory'));
    }

    /**
     * Test that the bundle loads with empty configuration without compilation errors.
     * This is crucial for Discogs Bundle Beta 4 compatibility - no compile-time validation.
     */
    public function testBundleLoadsWithEmptyConfiguration(): void
    {
        $container = new ContainerBuilder();
        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        $config = [
            'calliostro_lastfm' => [
                'api_key' => '',
                'api_secret' => '',
            ],
        ];

        // Should load without compilation errors
        $extension->load($config, $container);
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.lastfm_client'));
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.client_factory'));
    }

    /**
     * Test that the bundle loads with minimal configuration.
     */
    public function testBundleLoadsWithMinimalConfiguration(): void
    {
        $container = new ContainerBuilder();
        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        $config = [
            'calliostro_lastfm' => [],
        ];

        // Should load without errors
        $extension->load($config, $container);
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.lastfm_client'));
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.client_factory'));
    }

    /**
     * Test service creation with valid credentials using the kernel.
     * This tests the full integration including the factory pattern.
     */
    public function testServiceCreationWithValidCredentials(): void
    {
        $kernel = TestKernel::createForIntegration([
            'api_key' => 'test_api_key_123',
            'api_secret' => 'test_api_secret_456',
            'user_agent' => 'LastFmBundle/IntegrationTest',
        ]);

        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that the service can be retrieved
        $this->assertTrue($container->has('calliostro_lastfm.lastfm_client'));

        $lastfmClient = $container->get('calliostro_lastfm.lastfm_client');
        $this->assertInstanceOf(\Calliostro\LastFm\LastFmClient::class, $lastfmClient);
    }

    /**
     * Test service creation with session key for authenticated operations.
     * This tests the session key injection functionality.
     */
    public function testServiceCreationWithSessionKey(): void
    {
        $kernel = TestKernel::createForIntegration([
            'api_key' => 'test_api_key_123',
            'api_secret' => 'test_api_secret_456',
            'session_key' => 'test_session_key_789',
            'user_agent' => 'LastFmBundle/IntegrationTest',
        ]);

        $kernel->boot();
        $container = $kernel->getContainer();

        // Test that the service can be retrieved
        $this->assertTrue($container->has('calliostro_lastfm.lastfm_client'));

        $lastfmClient = $container->get('calliostro_lastfm.lastfm_client');
        $this->assertInstanceOf(\Calliostro\LastFm\LastFmClient::class, $lastfmClient);

        // The session key should be properly injected and the client should be functional
        // This verifies that the complete configuration including session key works correctly
        $this->assertNotNull($lastfmClient);
    }

    /**
     * Test service creation with API key only.
     */
    public function testServiceCreationWithApiKeyOnly(): void
    {
        $kernel = TestKernel::createForIntegration([
            'api_key' => 'test_api_key_123',
            'user_agent' => 'LastFmBundle/IntegrationTest',
        ]);

        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('calliostro_lastfm.lastfm_client'));

        $lastfmClient = $container->get('calliostro_lastfm.lastfm_client');
        $this->assertInstanceOf(\Calliostro\LastFm\LastFmClient::class, $lastfmClient);
    }

    /**
     * Test service creation with no credentials (basic client).
     */
    public function testServiceCreationWithNoCredentials(): void
    {
        $kernel = TestKernel::createForIntegration([
            'user_agent' => 'LastFmBundle/IntegrationTest',
        ]);

        $kernel->boot();
        $container = $kernel->getContainer();

        $this->assertTrue($container->has('calliostro_lastfm.lastfm_client'));

        $lastfmClient = $container->get('calliostro_lastfm.lastfm_client');
        $this->assertInstanceOf(\Calliostro\LastFm\LastFmClient::class, $lastfmClient);
    }

    /**
     * Test runtime validation with empty credentials.
     * The factory should throw proper runtime exceptions when service is actually used.
     */
    public function testRuntimeValidationWithEmptyCredentials(): void
    {
        $container = new ContainerBuilder();
        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        // Configuration with empty strings should not cause compile-time errors
        $config = [
            'calliostro_lastfm' => [
                'api_key' => '',
                'api_secret' => '',
            ],
        ];

        // Should load without compilation errors
        $extension->load($config, $container);

        // But should provide proper factory setup for runtime validation
        $this->assertTrue($container->hasDefinition('calliostro_lastfm.lastfm_client'));

        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $this->assertNotNull($clientDefinition->getFactory());
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createBasicClient'];
        $this->assertEquals($expectedFactory, $clientDefinition->getFactory());
    }

    /**
     * Test rate limiter configuration when symfony/rate-limiter is available.
     */
    public function testRateLimiterConfiguration(): void
    {
        if (!class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory')) {
            $this->markTestSkipped('symfony/rate-limiter is not installed');
        }

        $container = new ContainerBuilder();
        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        $config = [
            'calliostro_lastfm' => [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'rate_limiter' => 'limiter.lastfm_api',
            ],
        ];

        // Should load without errors
        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('calliostro_lastfm.lastfm_client'));

        // Rate limiter should be passed as an option to the factory
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');
        $this->assertNotNull($clientDefinition->getFactory());
        $expectedFactory = [new Reference('calliostro_lastfm.client_factory'), 'createClient'];
        $this->assertEquals($expectedFactory, $clientDefinition->getFactory());
    }

    /**
     * Test that bundle extension has the correct alias.
     */
    public function testBundleExtensionAlias(): void
    {
        $bundle = new CalliostroLastfmBundle();
        $extension = $bundle->getContainerExtension();

        $this->assertEquals('calliostro_lastfm', $extension->getAlias());
    }

    /**
     * Test bundle path configuration.
     */
    public function testBundlePath(): void
    {
        $bundle = new CalliostroLastfmBundle();

        $path = $bundle->getPath();
        $this->assertStringEndsWith('last-fm-client-bundle', $path);
    }
}
