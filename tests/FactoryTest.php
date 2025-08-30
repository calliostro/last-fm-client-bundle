<?php

namespace Calliostro\LastFmClientBundle\Tests;

use Calliostro\LastFmClientBundle\AlbumFactory;
use Calliostro\LastFmClientBundle\ArtistFactory;
use Calliostro\LastFmClientBundle\AuthFactory;
use Calliostro\LastFmClientBundle\AuthServiceFactory;
use Calliostro\LastFmClientBundle\ClientFactory;
use Calliostro\LastFmClientBundle\TrackFactory;
use Calliostro\LastFmClientBundle\UserFactory;
use LastFmClient\Auth;
use LastFmClient\Client;
use LastFmClient\Service\Album;
use LastFmClient\Service\Artist;
use LastFmClient\Service\Auth as AuthService;
use LastFmClient\Service\Track;
use LastFmClient\Service\User;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    private function createTestAuth(?string $apiKey = 'test_api_key', ?string $secret = 'test_secret', ?string $session = null): Auth
    {
        return AuthFactory::getAuth($apiKey, $secret, $session);
    }

    private function createTestClient(?string $session = null): Client
    {
        $auth = $this->createTestAuth(session: $session);
        return ClientFactory::getClient($auth);
    }

    public function testAuthFactory(): void
    {
        $auth = $this->createTestAuth();
        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testAuthFactoryWithNullValues(): void
    {
        $auth = $this->createTestAuth(null, null, null);
        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testClientFactory(): void
    {
        $client = $this->createTestClient();
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testAlbumFactory(): void
    {
        $client = $this->createTestClient();
        $albumService = AlbumFactory::getAlbumService($client);
        $this->assertInstanceOf(Album::class, $albumService);
    }

    public function testArtistFactory(): void
    {
        $client = $this->createTestClient();
        $artistService = ArtistFactory::getArtistService($client);
        $this->assertInstanceOf(Artist::class, $artistService);
    }

    public function testAuthServiceFactory(): void
    {
        $client = $this->createTestClient();
        $authService = AuthServiceFactory::getAuthService($client);
        $this->assertInstanceOf(AuthService::class, $authService);
    }

    public function testTrackFactory(): void
    {
        $client = $this->createTestClient();
        $trackService = TrackFactory::getTrackService($client);
        $this->assertInstanceOf(Track::class, $trackService);
    }

    public function testUserFactory(): void
    {
        $client = $this->createTestClient();
        $userService = UserFactory::getUserService($client);
        $this->assertInstanceOf(User::class, $userService);
    }
}
