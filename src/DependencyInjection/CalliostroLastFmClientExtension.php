<?php

namespace Calliostro\LastFmClientBundle\DependencyInjection;

use Calliostro\LastFm\ClientFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as DependencyInjectionExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class CalliostroLastFmClientExtension extends DependencyInjectionExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container) ?? new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->getDefinition('calliostro_last_fm_client.api_client')
                  ->replaceArgument(0, $config['api_key'] ?? null)
                  ->replaceArgument(1, $config['secret'] ?? null)
                  ->replaceArgument(2, $config['http_client_options'] ?? []);

        // If a session key is configured, create an authenticated client
        if (!empty($config['session'])) {
            $container->getDefinition('calliostro_last_fm_client.api_client')
                ->setFactory([ClientFactory::class, 'createWithAuth'])
                ->setArguments([
                    $config['api_key'] ?? null,
                    $config['secret'] ?? null,
                    $config['session'],
                    $config['http_client_options'] ?? []
                ]);
        }
    }
}
