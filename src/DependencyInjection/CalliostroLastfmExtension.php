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

        if (!empty($config['api_key']) && !empty($config['api_secret'])) {
            // API Key and Secret authentication (recommended for applications)
            $clientDefinition->setFactory([new Reference('calliostro_lastfm.client_factory'), 'createClient']);
            $clientDefinition->setArguments([
                $config['api_key'],
                $config['api_secret'],
                $this->getClientOptions($container, $config),
                $config['session_key'] ?? null,
            ]);
        } elseif (!empty($config['api_key'])) {
            // API Key only authentication (read-only operations)
            $clientDefinition->setFactory([new Reference('calliostro_lastfm.client_factory'), 'createClientWithApiKey']);
            $clientDefinition->setArguments([
                $config['api_key'],
                $this->getClientOptions($container, $config),
            ]);
        } else {
            // Create basic client without authentication (very limited functionality)
            $clientDefinition->setFactory([new Reference('calliostro_lastfm.client_factory'), 'createBasicClient']);
            $clientDefinition->setArguments([
                $this->getClientOptions($container, $config),
            ]);
        }
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
     * Just pass the RateLimiterFactory service to the client options like in discogs-bundle v4.0.0-beta.
     *
     * @param array<string, mixed> &$options
     */
    private function configureSymfonyRateLimiter(ContainerBuilder $container, string $rateLimiterService, array &$options): void
    {
        // Check if the symfony/rate-limiter component is available
        if (!$this->isRateLimiterAvailable()) {
            throw new \LogicException('To use the rate_limiter configuration, you must install symfony/rate-limiter. Run: composer require symfony/rate-limiter');
        }

        // Simply pass the RateLimiterFactory service reference to client options
        // The underlying lastfm-client will handle rate limiting internally if it supports it
        $options['rate_limiter'] = new Reference($rateLimiterService);
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
     */
    private function isRateLimiterAvailable(): bool
    {
        return class_exists('Symfony\\Component\\RateLimiter\\RateLimiterFactory');
    }
}
