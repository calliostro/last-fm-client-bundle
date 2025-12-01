<?php

declare(strict_types=1);

namespace Calliostro\LastfmBundle\DependencyInjection;

use Calliostro\LastFm\LastFmClient;

/**
 * Factory service for creating LastFmClient instances with runtime validation.
 * This handles credential validation at service creation time rather than
 * container compilation time, allowing environment variables to be resolved.
 */
final class LastFmClientFactory
{
    /**
     * Create a LastFmClient with runtime credential validation.
     *
     * @param array<string, mixed> $options
     */
    public function createClient(
        ?string $apiKey,
        ?string $apiSecret,
        ?string $sessionKey,
        array $options = [],
    ): LastFmClient {
        // Trim all credentials to handle whitespace-only values and null
        $apiKey = $apiKey ? trim($apiKey) : '';
        $apiSecret = $apiSecret ? trim($apiSecret) : '';
        $sessionKey = $sessionKey ? trim($sessionKey) : '';

        // Validate and create client based on available credentials
        if (!empty($apiKey) && !empty($apiSecret)) {
            return $this->createWithApiKeyAndSecret($apiKey, $apiSecret, $sessionKey, $options);
        }

        if (!empty($apiKey)) {
            return $this->createWithApiKeyOnly($apiKey, $options);
        }

        // Check for partial credentials and provide helpful error
        if (!empty($apiSecret)) {
            throw new \InvalidArgumentException('Incomplete API credentials provided. API key is required when API secret is provided. '.$this->getSetupInstructions());
        }

        // Create anonymous client (rate-limited) - this is allowed
        return new LastFmClient($options);
    }

    /**
     * Create client with API Key and Secret and validate them.
     *
     * @param array<string, mixed> $options
     */
    private function createWithApiKeyAndSecret(string $apiKey, string $apiSecret, ?string $sessionKey, array $options): LastFmClient
    {
        if (\strlen($apiKey) < 10) {
            throw new \InvalidArgumentException(\sprintf('API key must be at least 10 characters long, got %d characters. %s', \strlen($apiKey), $this->getSetupInstructions()));
        }

        if (\strlen($apiSecret) < 10) {
            throw new \InvalidArgumentException(\sprintf('API secret must be at least 10 characters long, got %d characters. %s', \strlen($apiSecret), $this->getSetupInstructions()));
        }

        $client = new LastFmClient($options);
        $client->setApiCredentials($apiKey, $apiSecret, $sessionKey);

        return $client;
    }

    /**
     * Create client with API Key only.
     *
     * @param array<string, mixed> $options
     */
    private function createWithApiKeyOnly(string $apiKey, array $options): LastFmClient
    {
        if (\strlen($apiKey) < 10) {
            throw new \InvalidArgumentException(\sprintf('API key must be at least 10 characters long, got %d characters. %s', \strlen($apiKey), $this->getSetupInstructions()));
        }

        $client = new LastFmClient($options);
        $client->setApiCredentials($apiKey);

        return $client;
    }

    /**
     * Get helpful setup instructions for the user.
     */
    private function getSetupInstructions(): string
    {
        return "\n\nTo configure Last.fm API credentials:\n".
               "1. API Key and Secret (recommended for full functionality):\n".
               "   - Get your credentials from: https://www.last.fm/api/account/create\n".
               "   - Set environment variables:\n".
               "     LASTFM_API_KEY=your_key_here\n".
               "     LASTFM_API_SECRET=your_secret_here\n".
               "   - Configure in config/packages/calliostro_lastfm.yaml:\n".
               "     calliostro_lastfm:\n".
               "       api_key: '%env(LASTFM_API_KEY)%'\n".
               "       api_secret: '%env(LASTFM_API_SECRET)%'\n\n".
               "2. API Key only (read-only operations):\n".
               "   - Set environment variable:\n".
               "     LASTFM_API_KEY=your_key_here\n".
               "   - Configure in config/packages/calliostro_lastfm.yaml:\n".
               "     calliostro_lastfm:\n".
               "       api_key: '%env(LASTFM_API_KEY)%'\n\n".
               "3. Anonymous access (very limited functionality):\n".
               "   - No configuration needed, but only basic public endpoints available\n";
    }
}
