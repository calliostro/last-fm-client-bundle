<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\Factory;

use Calliostro\LastFm\LastFmClient;

/**
 * Factory class for creating LastFmClient instances with proper validation.
 *
 * Provides simple factory methods for different authentication scenarios.
 */
final class LastFmClientFactory
{
    /**
     * Create a LastFmClient with API key and secret authentication.
     *
     * This is the recommended method for applications that need write access
     * to the Last.fm API (such as scrobbling).
     *
     * @param string               $apiKey     The Last.fm API key
     * @param string               $apiSecret  The Last.fm API secret
     * @param array<string, mixed> $options    Additional client options
     * @param string|null          $sessionKey Optional session key for authenticated user operations
     *
     * @throws \InvalidArgumentException When credentials are invalid or missing
     */
    public function createClient(
        string $apiKey,
        string $apiSecret,
        array $options = [],
        ?string $sessionKey = null,
    ): LastFmClient {
        // Runtime validation with clear error messages
        if (empty($apiKey) || '' === trim($apiKey)) {
            throw new \InvalidArgumentException('Last.fm API key is required. Please configure LASTFM_API_KEY environment variable. Get your API key from: https://www.last.fm/api/account/create');
        }

        if (empty($apiSecret) || '' === trim($apiSecret)) {
            throw new \InvalidArgumentException('Last.fm API secret is required for authenticated operations. Please configure LASTFM_SECRET environment variable. Get your API secret from: https://www.last.fm/api/account/create');
        }

        $client = new LastFmClient($options);
        $client->setApiCredentials($apiKey, $apiSecret, $sessionKey);

        return $client;
    }

    /**
     * Create a LastFmClient with API key only authentication.
     *
     * This method is suitable for read-only operations that don't require
     * write access to the Last.fm API.
     *
     * @param string               $apiKey  The Last.fm API key
     * @param array<string, mixed> $options Additional client options
     *
     * @throws \InvalidArgumentException When API key is invalid or missing
     */
    public function createClientWithApiKey(
        string $apiKey,
        array $options = [],
    ): LastFmClient {
        if (empty($apiKey) || '' === trim($apiKey)) {
            throw new \InvalidArgumentException('Last.fm API key is required. Please configure LASTFM_API_KEY environment variable. Get your API key from: https://www.last.fm/api/account/create');
        }

        $client = new LastFmClient($options);
        $client->setApiCredentials($apiKey);

        return $client;
    }

    /**
     * Create a basic LastFmClient without authentication.
     *
     * This method creates a client with very limited functionality,
     * suitable only for public endpoints that don't require authentication.
     *
     * @param array<string, mixed> $options Additional client options
     */
    public function createBasicClient(array $options = []): LastFmClient
    {
        return new LastFmClient($options);
    }
}
