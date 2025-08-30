<?php

namespace Calliostro\LastFmClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CalliostroLastFmClientExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container) ?? new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->getDefinition('calliostro_last_fm_client.auth')
                  ->replaceArgument(0, $config['api_key'] ?? null)
                  ->replaceArgument(1, $config['secret'] ?? null)
                  ->replaceArgument(2, $config['session'] ?? null);

        $container->getDefinition('calliostro_last_fm_client.client')
            ->replaceArgument(0, new Reference('calliostro_last_fm_client.auth'));

        $clientReference = new Reference('calliostro_last_fm_client.client');

        $container->getDefinition('calliostro_last_fm_client.auth_service')
            ->replaceArgument(0, $clientReference);

        foreach (['artist', 'album', 'track', 'user'] as $type) {
            $container->getDefinition('calliostro_last_fm_client.' . $type)
                      ->replaceArgument(0, $clientReference);
        }
    }
}
