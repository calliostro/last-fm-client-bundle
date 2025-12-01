<?php

namespace Calliostro\LastfmBundle\Tests\Integration;

/**
 * Integration Tests for Public API Endpoints.
 *
 * These tests run against the real Last.fm API using public endpoints
 * that require only API key authentication. They validate:
 *
 * 1. Bundle service wiring with real API
 * 2. Response format consistency through Bundle
 * 3. Bundle configuration handling
 *
 * Requires API key for CI/CD!
 */
final class PublicApiIntegrationTest extends IntegrationTestCase
{
    /**
     * Test basic API methods that should always work through Bundle.
     */
    public function testBasicApiMethods(): void
    {
        // Test artist info - using real Last.fm API method
        $artist = $this->client->getArtistInfo(artist: 'Ed Sheeran');
        $this->assertArrayHasKey('artist', $artist);
        $this->assertArrayHasKey('name', $artist['artist']);

        // Test track info
        $track = $this->client->getTrackInfo(artist: 'Adele', track: 'Someone Like You');
        $this->assertArrayHasKey('track', $track);
        $this->assertArrayHasKey('name', $track['track']);

        // Test album info
        $album = $this->client->getAlbumInfo(artist: 'Coldplay', album: 'A Head Full of Dreams');
        $this->assertArrayHasKey('album', $album);
        $this->assertArrayHasKey('name', $album['album']);
    }

    /**
     * Test that the bundle correctly uses environment variables.
     */
    public function testEnvironmentVariableUsage(): void
    {
        // Skip if no real API key provided
        if (empty(getenv('LASTFM_API_KEY'))) {
            $this->markTestSkipped('No LASTFM_API_KEY environment variable provided');
        }

        // This should use the real environment variables
        $this->assertNotEmpty(getenv('LASTFM_API_KEY'));
        $this->assertTrue(\strlen(getenv('LASTFM_API_KEY')) > 10);
    }

    /**
     * Test Bundle's basic functionality without rate limiting.
     * The Bundle is ultra-lightweight and doesn't include built-in throttling.
     * Rate limiting can be added via Symfony's rate-limiter component as needed.
     */
    public function testBundleBasicFunctionality(): void
    {
        // Create a kernel with minimal configuration
        $kernel = $this->createKernel([
            'api_key' => getenv('LASTFM_API_KEY') ?: 'test_key',
            'api_secret' => getenv('LASTFM_SECRET') ?: null,
            'user_agent' => 'CalliostroLastfmBundle/IntegrationTest',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        // Verify the client works without any rate limiting
        $client = $container->get('calliostro_lastfm.lastfm_client');
        \assert($client instanceof \Calliostro\LastFm\LastFmClient);

        // Make requests - Bundle is ultra-lightweight with no built-in throttling
        $responses = [];
        for ($i = 0; $i < 2; ++$i) {
            $responses[] = $client->getArtistInfo(artist: 'Billie Eilish');
        }

        // All requests should succeed
        $this->assertCount(2, $responses);
        foreach ($responses as $response) {
            $this->assertArrayHasKey('artist', $response);
            $this->assertArrayHasKey('name', $response['artist']);
        }

        // Bundle is ultra-lightweight - no built-in throttling overhead
        $this->addToAssertionCount(1); // Ultra-lightweight bundle allows rapid requests
    }

    /**
     * Test Bundle error handling for invalid artist through real API.
     */
    public function testBundleErrorHandling(): void
    {
        $this->expectException(\Exception::class);
        $this->client->getArtistInfo(artist: 'ThisArtistDefinitelyDoesNotExist123456789');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = $this->createKernel([
            'api_key' => getenv('LASTFM_API_KEY') ?: 'test_key',
            'api_secret' => getenv('LASTFM_SECRET') ?: null,
            'user_agent' => 'CalliostroLastfmBundle/IntegrationTest',
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();

        $client = $container->get('calliostro_lastfm.lastfm_client');
        \assert($client instanceof \Calliostro\LastFm\LastFmClient);
        $this->client = $client;
    }
}
