<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('calliostro_lastfm');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('api_key')
            ->info('Your Last.fm API key (required - get from https://www.last.fm/api/account/create)')
            ->end()
            ->scalarNode('api_secret')
            ->info('Your Last.fm API secret (required for authenticated operations)')
            ->end()
            ->scalarNode('session_key')
            ->defaultNull()
            ->info('Your Last.fm session key (optional - for authenticated user operations)')
            ->end()
            ->scalarNode('user_agent')
            ->defaultNull()
            ->info('HTTP User-Agent header for API requests (optional)')
            ->validate()
            ->ifTrue(fn ($v) => \is_string($v) && \strlen($v) > 200)
            ->thenInvalid('User-Agent cannot be longer than 200 characters')
            ->end()
            ->end()
            ->scalarNode('rate_limiter')
            ->defaultNull()
            ->info('Symfony RateLimiterFactory service ID for advanced rate limiting (requires symfony/rate-limiter)')
            ->validate()
            ->ifTrue(fn ($v) => \is_string($v) && '' === trim($v))
            ->thenInvalid('Rate limiter service ID cannot be empty')
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
