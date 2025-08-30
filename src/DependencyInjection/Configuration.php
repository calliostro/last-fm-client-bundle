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
                    ->info('Get your API credentials from https://www.last.fm/api/account/create')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('secret')
                    ->info('API secret from your Last.fm application')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('session')
                    ->info('Optional: session key for user-specific actions (scrobbling, etc.)')
                ->end()
                ->arrayNode('http_client_options')
                    ->info(
                        "Optional: HTTP client configuration\n" .
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
