# ⚡ Last.fm Client Bundle for Symfony – Ultra-Lightweight

[![Latest Stable Version](https://img.shields.io/packagist/v/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![License](https://img.shields.io/packagist/l/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![CI (Main)](https://github.com/calliostro/last-fm-client-bundle/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/calliostro/last-fm-client-bundle/actions)
[![codecov](https://codecov.io/gh/calliostro/last-fm-client-bundle/graph/badge.svg?branch=main)](https://codecov.io/gh/calliostro/last-fm-client-bundle?branch=main)

> **🚀 SYMFONY INTEGRATION!** Seamless autowiring for the most lightweight Last.fm API client for PHP. Zero bloats, maximum performance.

Symfony bundle that integrates the **ultra-minimalist** [calliostro/lastfm-client](https://github.com/calliostro/lastfm-client) into your Symfony application. Built with modern PHP 8.1+ features, dependency injection, and powered by Guzzle.

## 📦 Installation

Install via Composer:

```bash
composer require calliostro/last-fm-client-bundle
```

## ⚙️ Configuration

Configure the bundle in `config/packages/calliostro_last_fm_client.yaml`:

```yaml
calliostro_last_fm_client:
    # Get your API credentials from https://www.last.fm/api/account/create
    api_key: '%env(LASTFM_API_KEY)%'
    secret: '%env(LASTFM_SECRET)%'
    
    # Optional: pre-configured session key for user authentication
    # session: '%env(LASTFM_SESSION_KEY)%'
    
    # Optional: HTTP client options
    # http_client_options:
    #     timeout: 30
    #     headers:
    #         'User-Agent': 'MyApp/1.0'
```

**API Key & Secret:** You need to [register your application](https://www.last.fm/api/account/create) at Last.fm to get your API key and secret. Both values are **required** and cannot be empty.

**Session Key:** This version supports only a pre-configured user session for scrobbling and user-specific actions. For read-only operations (artist info, charts, search), you're all set with just an API key and secret. For full OAuth workflow support, use the standalone [calliostro/lastfm-client](https://github.com/calliostro/lastfm-client) library.

**User-Agent:** By default, the client uses `LastFmClient/1.0 (+https://github.com/calliostro/lastfm-client)` as User-Agent. You can override this in the `http_client_options` if needed.

## 🚀 Quick Start

### Basic Usage

```php
<?php
// src/Controller/MusicController.php

namespace App\Controller;

use Calliostro\LastFm\LastFmApiClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class MusicController
{
    public function artistInfo(string $name, LastFmApiClient $client): JsonResponse
    {
        $artist = $client->artistGetInfo(['artist' => $name]);
        $topTracks = $client->artistGetTopTracks(['artist' => $name, 'limit' => 5]);

        return new JsonResponse([
            'artist' => $artist['artist']['name'],
            'topTracks' => $topTracks['toptracks']['track']
        ]);
    }
}
```

### Scrobbling and User Actions

```php
// Requires pre-configured session key
$client->trackUpdateNowPlaying([
    'artist' => 'Linkin Park',
    'track' => 'In the End'
]);

$client->trackScrobble([
    'artist' => 'Linkin Park',
    'track' => 'In the End',
    'timestamp' => time()
]);

$client->trackLove(['artist' => 'Adele', 'track' => 'Hello']);
```

### Discovery and Charts

```php
$similar = $client->artistGetSimilar(['artist' => 'Imagine Dragons']);
$topTracks = $client->artistGetTopTracks(['artist' => 'Adele']);
$recentTracks = $client->userGetRecentTracks(['user' => 'username']);
$topArtists = $client->chartGetTopArtists(['limit' => 10]);
$rockTracks = $client->tagGetTopTracks(['tag' => 'rock']);
```

## ✨ Key Features

- **Ultra-Lightweight** – Minimal Symfony integration with zero bloats for the ultra-lightweight Last.fm client
- **Complete API Coverage** – All 60+ Last.fm API endpoints supported
- **Direct API Calls** – `$client->trackGetInfo()` maps to `track.getInfo`, no abstractions
- **Type Safe + IDE Support** – Full PHP 8.1+ types, PHPStan Level 8, method autocomplete
- **Symfony Native** – Seamless autowiring with Symfony 6.4, 7.x & 8.x
- **Future-Ready** – PHP 8.5 and Symfony 8.0 compatible (beta/dev testing)
- **Well Tested** – 100% test coverage, PSR-12 compliant
- **Configuration-Based Auth** – Pre-configured session key support

## 🎵 All Last.fm API Methods as Direct Calls

- **Track Methods** – trackGetInfo(), trackScrobble(), trackUpdateNowPlaying(), trackLove(), trackUnlove()
- **Artist Methods** – artistGetInfo(), artistGetTopTracks(), artistGetSimilar(), artistSearch()
- **User Methods** – userGetInfo(), userGetRecentTracks(), userGetLovedTracks(), userGetTopArtists()
- **Chart Methods** – chartGetTopArtists(), chartGetTopTracks()
- **Album Methods** – albumGetInfo(), albumSearch()
- **Tag Methods** – tagGetInfo(), tagGetTopTracks(), tagGetTopTags()
- **Auth Methods** – authGetToken(), authGetSession()
- **Geo Methods** – geoGetTopArtists(), geoGetTopTracks()
- **Library Methods** – libraryGetArtists()

*All 60+ Last.fm API endpoints are supported with clean documentation — see [Last.fm API Documentation](https://www.last.fm/api) for complete method reference*

## 📋 Requirements

- php ^8.1
- symfony ^6.4|^7.0|^8.0
- calliostro/lastfm-client

## 🔧 Service Integration

```php
<?php
// src/Service/MusicService.php

namespace App\Service;

use Calliostro\LastFm\LastFmApiClient;

class MusicService
{
    public function __construct(
        private readonly LastFmApiClient $client
    ) {}

    public function scrobbleTrack(string $artist, string $track): void
    {
        // Requires pre-configured session key
        $this->client->trackScrobble([
            'artist' => $artist,
            'track' => $track,
            'timestamp' => time()
        ]);
    }

    public function updateNowPlaying(string $artist, string $track): void
    {
        $this->client->trackUpdateNowPlaying([
            'artist' => $artist,
            'track' => $track
        ]);
    }
}
```

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Run static analysis:

```bash
composer analyse
```

Check code style:

```bash
composer cs
```

## 📖 API Documentation Reference

For complete API documentation including all available parameters, visit the [Last.fm API Documentation](https://www.last.fm/api).

### Popular Methods

#### Track Methods

- `trackGetInfo($params)` – Get track information
- `trackSearch($params)` – Search for tracks
- `trackScrobble($params)` – Scrobble a track (auth required)
- `trackUpdateNowPlaying($params)` – Update now playing (auth required)
- `trackLove($params)` – Love a track (auth required)
- `trackUnlove($params)` – Remove love from track (auth required)

#### Artist Methods

- `artistGetInfo($params)` – Get artist information
- `artistGetTopTracks($params)` – Get artist's top tracks
- `artistGetSimilar($params)` – Get similar artists
- `artistSearch($params)` – Search for artists

#### User Methods

- `userGetInfo($params)` – Get user profile information
- `userGetRecentTracks($params)` – Get user's recent tracks
- `userGetLovedTracks($params)` – Get user's loved tracks
- `userGetTopArtists($params)` – Get user's top artists

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows PSR-12 standards and includes tests.

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Last.fm](https://last.fm) for providing the excellent music data API
- [Symfony](https://symfony.com) for the robust framework and DI container
- [calliostro/lastfm-client](https://github.com/calliostro/lastfm-client) for the ultra-lightweight client library

---

> **⭐ Star this repo** if you find it useful! It helps others discover this lightweight solution.
