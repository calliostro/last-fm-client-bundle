<?php

namespace Calliostro\LastFmClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('calliostro_last_fm_client');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->defaultValue('')
                    ->info('Your API key')
                ->end()
                ->scalarNode('secret')
                    ->defaultValue('')
                    ->info('Your secret')
                ->end()
                ->scalarNode('token')
                    ->defaultNull()
                    ->info('Optionally a fixed user token')
                    ->setDeprecated('calliostro/last-fm-client-bundle', '0.1.1', 'Will be removed in version 0.2.0')
                ->end()
                ->scalarNode('session')
                    ->defaultNull()
                    ->info('Optionally a fixed user session')
                    ->setDeprecated('calliostro/last-fm-client-bundle', '0.1.1', 'Will be removed in version 0.2.0')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
