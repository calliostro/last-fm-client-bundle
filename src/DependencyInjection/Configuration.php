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
                ->scalarNode('api_key')->info('Your API key')->end()
                ->scalarNode('secret')->info('Your secret')->end()
                ->scalarNode('token')->defaultNull()->info('Optionally a fixed user token')->end()
                ->scalarNode('session')->defaultNull()->info('Optionally a fixed user session')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
