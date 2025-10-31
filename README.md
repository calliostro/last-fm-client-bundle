# ⚡ Last.fm Client Bundle for Symfony – Complete Music Scrobbling & Data Access

[![Package Version](https://img.shields.io/packagist/v/calliostro/lastfm-bundle.svg)](https://packagist.org/packages/calliostro/lastfm-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/lastfm-bundle.svg)](https://packagist.org/packages/calliostro/lastfm-bundle)
[![License](https://poser.pugx.org/calliostro/lastfm-bundle/license)](https://packagist.org/packages/calliostro/lastfm-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![CI](https://github.com/calliostro/lastfm-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/calliostro/lastfm-bundle/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/calliostro/lastfm-bundle/graph/badge.svg?token=3ATEFYF7A0)](https://codecov.io/gh/calliostro/lastfm-bundle)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![Code Style](https://img.shields.io/badge/code%20style-Symfony-brightgreen.svg)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

> **🚀 SYMFONY INTEGRATION!** Seamless autowiring for the complete Last.fm music API. Zero bloat, maximum performance.

Symfony bundle that integrates the **modern** [calliostro/lastfm-client](https://github.com/calliostro/lastfm-client) into your Symfony application. Built with modern PHP 8.1+ features, dependency injection, and powered by Guzzle.

## 📦 Installation

Install via Composer:

```bash
composer require calliostro/lastfm-bundle
```

## ⚙️ Configuration

Configure the bundle in `config/packages/calliostro_lastfm.yaml`:

```yaml
calliostro_lastfm:
    # Required: API Key (get from https://www.last.fm/api/account/create)
    api_key: '%env(LASTFM_API_KEY)%'
    
    # Required for write operations: API Secret  
    api_secret: '%env(LASTFM_SECRET)%'
    
    # Optional: HTTP User-Agent header for API requests
    # user_agent: 'MyApp/1.0 +https://myapp.com'
    
    # Optional: Professional rate limiting (requires symfony/rate-limiter)
    # rate_limiter: lastfm_api       # Your configured RateLimiterFactory service
```

**API Key:** You need to [create an API account](https://www.last.fm/api/account/create) at Last.fm to get your API key. This is required for all operations.

**API Secret:** Required for write operations like scrobbling tracks, loving tracks, or posting shouts.

**User-Agent:** By default, the client uses `LastfmClient/2.0.0 (+https://github.com/calliostro/lastfm-client)` as User-Agent. You can override this in the configuration if needed.

## 🚀 Quick Start

### Basic Usage

```php
<?php
// src/Controller/MusicController.php

namespace App\Controller;

use Calliostro\Lastfm\LastfmClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class MusicController
{
    public function artistInfo(string $artist, LastfmClient $client): JsonResponse
    {
        $artistInfo = $client->getArtistInfo(artist: $artist);
        $topTracks = $client->getArtistTopTracks(artist: $artist, limit: 5);

        return new JsonResponse([
            'artist' => $artistInfo['artist']['name'],
            'bio' => $artistInfo['artist']['bio']['summary'] ?? null,
            'topTracks' => $topTracks['toptracks']['track'],
        ]);
    }
}
```

### Scrobbling and User Data

```php
// Requires API Key and Secret + User Authentication
// Scrobble and love tracks with named parameters
$lastfm->scrobbleTrack(
    artist: 'Ed Sheeran',
    track: 'Shape of You',
    timestamp: time()
);

$client->loveTrack(artist: 'Adele', track: 'Someone Like You');

$recentTracks = $client->getUserRecentTracks(user: 'username', limit: 10);
$topArtists = $client->getUserTopArtists(user: 'username', period: '1month');
```

### Music Discovery

```php
$artistInfo = $client->getArtistInfo(artist: 'Ed Sheeran');
$albumInfo = $client->getAlbumInfo(artist: 'Taylor Swift', album: 'Midnights');
$trackInfo = $client->getTrackInfo(artist: 'The Weeknd', track: 'Blinding Lights');

$similarArtists = $client->getArtistSimilar(artist: 'Ed Sheeran');
$topTracks = $client->getArtistTopTracks(artist: 'Bruno Mars', limit: 10);
$topAlbums = $client->getArtistTopAlbums(artist: 'Coldplay');
```

## ✨ Key Features

- **Ultra-Lightweight** – Minimal Symfony integration with zero bloat for the ultra-lightweight Last.fm client
- **Complete API Coverage** – All Last.fm API endpoints supported (Album, Artist, Auth, Chart, Geo, Library, Tag, Track, User)
- **Direct API Calls** – `$client->getArtistInfo(artist: 'name')` maps to `/2.0/?method=artist.getinfo`, no abstractions
- **Type Safe + IDE Support** – Full PHP 8.1+ types, PHPStan Level 8, method autocomplete  
- **Symfony Native** – Seamless autowiring with Symfony 6.4, 7.x & 8.x
- **Future-Ready** – PHP 8.5 and Symfony 8.0 compatible (beta/dev testing)
- **Well Tested** – Comprehensive test coverage, Symfony coding standards
- **Flexible Auth** – API Key for read operations, API Key + Secret for write operations

## 🎵 All Last.fm API Methods as Direct Calls

- **Album Methods** – getAlbumInfo(), addAlbumTags(), getAlbumTags(), getAlbumTopTags(), removeAlbumTag(), searchAlbums()
- **Artist Methods** – getArtistInfo(), getArtistCorrection(), getArtistSimilar(), getArtistTags(), getArtistTopAlbums(), getArtistTopTags(), getArtistTopTracks(), addArtistTags(), removeArtistTag(), searchArtists()
- **Auth Methods** – getMobileSession(), getSession(), getToken()
- **Chart Methods** – getTopArtists(), getTopTags(), getTopTracks()
- **Geo Methods** – getTopArtists(), getTopTracks()
- **Library Methods** – getArtists()
- **Tag Methods** – getInfo(), getSimilar(), getTopAlbums(), getTopArtists(), getTopTracks(), getWeeklyChartList()
- **Track Methods** – getTrackInfo(), getTrackCorrection(), getTrackSimilar(), getTrackTags(), getTrackTopTags(), addTrackTags(), loveTrack(), removeTrackTag(), scrobbleTrack(), unloveTrack(), updateNowPlaying(), searchTracks()
- **User Methods** – getUserInfo(), getUserFriends(), getUserLovedTracks(), getUserPersonalTags(), getUserRecentTracks(), getUserTopAlbums(), getUserTopArtists(), getUserTopTags(), getUserTopTracks(), getUserWeeklyAlbumChart(), getUserWeeklyArtistChart(), getUserWeeklyChartList(), getUserWeeklyTrackChart()

*All Last.fm API endpoints are supported with clean documentation — see [Last.fm API Documentation](https://www.last.fm/api) for complete method reference*

## 📋 Requirements

- php ^8.1
- symfony ^6.4 | ^7.0 | ^8.0
- calliostro/lastfm-client ^2.0

## 🔧 Service Integration

```php
<?php
// src/Service/MusicService.php

namespace App\Service;

use Calliostro\Lastfm\LastfmClient;

class MusicService
{
    public function __construct(
        private readonly LastfmClient $client
    ) {
    }

    public function getArtistWithTopTracks(string $artist): array
    {
        $artistInfo = $this->client->getArtistInfo(artist: $artist);
        $topTracks = $this->client->getArtistTopTracks(
            artist: $artist,
            limit: 10
        );

        return [
            'artist' => $artistInfo,
            'topTracks' => $topTracks['toptracks']['track'],
        ];
    }

    public function scrobbleCurrentTrack(string $artist, string $track): void
    {
        // Requires API Key + Secret + User Authentication
        $this->client->scrobbleTrack(
            artist: $artist,
            track: $track,
            timestamp: time()
        );
    }
}
```

## ⚡ Rate Limiting (Optional)

For high-volume applications, use the powerful [symfony/rate-limiter](https://symfony.com/doc/current/rate_limiter.html) component:

```bash
composer require symfony/rate-limiter
```

### 1. Configure Rate Limiter

```yaml
# config/packages/rate_limiter.yaml
rate_limiter:
    lastfm_api:
        policy: 'sliding_window'
        limit: 5  # Last.fm allows 5 requests per second per IP
        interval: '1 second'
```

### 2. Configure Bundle

```yaml
# config/packages/calliostro_lastfm.yaml
calliostro_lastfm:
    api_key: '%env(LASTFM_API_KEY)%'
    api_secret: '%env(LASTFM_SECRET)%'
    rate_limiter: lastfm_api
```

**Note on rate limits:**

Last.fm officially allows 5 requests per second per IP address. Higher rates may result in HTTP 429 responses.

## 🤝 Contributing

Contributions are welcome! Please see [DEVELOPMENT.md](DEVELOPMENT.md) for detailed setup instructions, testing guide, and development workflow.

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Last.fm](https://www.last.fm/) for providing the excellent music scrobbling and data API
- [Symfony](https://symfony.com) for the robust framework and DI container
- [calliostro/lastfm-client](https://github.com/calliostro/lastfm-client) for the modern client library

---

> **⭐ Star this repo** if you find it useful! It helps others discover this lightweight solution.
