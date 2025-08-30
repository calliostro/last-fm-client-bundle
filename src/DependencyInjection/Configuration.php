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

        // @phpstan-ignore-next-line: Suppress type error for $rootNode chaining (Symfony config definition)
        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->defaultValue('')
                ->end()
                ->scalarNode('secret')
                    ->defaultValue('')
                ->end()
                ->scalarNode('session')
                    ->info('Optional: pre-configured session key for user authentication')
                ->end()
                ->arrayNode('http_client_options')
                    ->info(
                        "Optional: HTTP client options\n" .
                        "See: https://docs.guzzlephp.org/en/stable/request-options.html"
                    )
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->variablePrototype()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
