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
    public function testAuthFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', 'test_session');
        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testAuthFactoryWithNullValues(): void
    {
        $auth = AuthFactory::getAuth(null, null, null);
        $this->assertInstanceOf(Auth::class, $auth);
    }

    public function testClientFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', null);
        $client = ClientFactory::getClient($auth);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testAlbumFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', null);
        $client = ClientFactory::getClient($auth);
        $albumService = AlbumFactory::getAlbumService($client);
        $this->assertInstanceOf(Album::class, $albumService);
    }

    public function testArtistFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', null);
        $client = ClientFactory::getClient($auth);
        $artistService = ArtistFactory::getArtistService($client);
        $this->assertInstanceOf(Artist::class, $artistService);
    }

    public function testAuthServiceFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', null);
        $client = ClientFactory::getClient($auth);
        $authService = AuthServiceFactory::getAuthService($client);
        $this->assertInstanceOf(AuthService::class, $authService);
    }

    public function testTrackFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', null);
        $client = ClientFactory::getClient($auth);
        $trackService = TrackFactory::getTrackService($client);
        $this->assertInstanceOf(Track::class, $trackService);
    }

    public function testUserFactory(): void
    {
        $auth = AuthFactory::getAuth('test_api_key', 'test_secret', null);
        $client = ClientFactory::getClient($auth);
        $userService = UserFactory::getUserService($client);
        $this->assertInstanceOf(User::class, $userService);
    }
}
