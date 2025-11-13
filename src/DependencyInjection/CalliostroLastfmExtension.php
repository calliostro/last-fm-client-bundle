<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class CalliostroLastfmExtension extends Extension
{
    public function getAlias(): string
    {
        return 'calliostro_lastfm';
    }

    /**
     * @throws \Exception When the XML service configuration file cannot be loaded
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Load services configuration
        $this->loadServices($container);

        // Configure client based on authentication method
        $this->configureClient($container, $config);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureClient(ContainerBuilder $container, array $config): void
    {
        $clientDefinition = $container->getDefinition('calliostro_lastfm.lastfm_client');

        // Use unified factory with runtime validation
        $clientDefinition->setFactory([new Reference('calliostro_lastfm.di_client_factory'), 'createClient']);
        $clientDefinition->setArguments([
            $config['api_key'] ?? null,
            $config['api_secret'] ?? null,
            $config['session_key'] ?? null,
            $this->getClientOptions($container, $config),
        ]);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function getClientOptions(ContainerBuilder $container, array $config): array
    {
        $options = [];

        // Only set the User-Agent header if explicitly configured
        if (!empty($config['user_agent'])) {
            $options['headers'] = ['User-Agent' => $config['user_agent']];
        }

        // Configure rate limiting if requested
        if (!empty($config['rate_limiter'])) {
            $this->configureSymfonyRateLimiter($container, $config['rate_limiter'], $options);
        }

        return $options;
    }

    /**
     * Configure Symfony Rate Limiter integration.
     *
     * @param array<string, mixed> &$options
     */
    private function configureSymfonyRateLimiter(ContainerBuilder $container, string $rateLimiterService, array &$options): void
    {
        // Check if the symfony/rate-limiter component is available
        if (!$this->isRateLimiterAvailable()) {
            throw new \LogicException('To use the rate_limiter configuration, you must install symfony/rate-limiter. Run: composer require symfony/rate-limiter');
        }

        // Create the rate limiter middleware service
        $middlewareDefinition = $container->register('calliostro_lastfm.rate_limiter_middleware', 'Calliostro\\LastfmBundle\\Middleware\\RateLimiterMiddleware');
        $middlewareDefinition->setArguments([
            new Reference($rateLimiterService),
            'lastfm_api', // Default limiter key
        ]);

        // Create a handler stack with the rate limiter middleware
        $handlerDefinition = $container->register('calliostro_lastfm.rate_limiter_handler_stack', 'GuzzleHttp\\HandlerStack');
        $handlerDefinition->setFactory(['GuzzleHttp\\HandlerStack', 'create']);
        $handlerDefinition->addMethodCall('push', [
            new Reference('calliostro_lastfm.rate_limiter_middleware'),
            'rate_limiter',
        ]);

        $options['handler'] = new Reference('calliostro_lastfm.rate_limiter_handler_stack');
    }

    /**
     * Load service configuration files.
     * Uses PHP configuration for all Symfony versions (4.2+) for consistency and future-proofing.
     */
    private function loadServices(ContainerBuilder $container): void
    {
        $fileLocator = new FileLocator(__DIR__.'/../Resources/config');
        $loader = new PhpFileLoader($container, $fileLocator);
        $loader->load('services.php');
    }

    /**
     * Check if the symfony/rate-limiter component is available.
     * This method is protected to allow testing.
     */
    protected function isRateLimiterAvailable(): bool
    {
        return class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory');
    }
}
