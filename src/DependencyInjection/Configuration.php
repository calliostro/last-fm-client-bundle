<?php

namespace Calliostro\LastFmClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('calliostro_last_fm_client');
        $rootNode = $treeBuilder->getRootNode();

        // @phpstan-ignore-next-line
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
                ->scalarNode('session')
                    ->info('Optionally a fixed user session (e.g. for scrobbling)')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
