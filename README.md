# Last.fm API Bundle for Symfony

[![Package Version](https://img.shields.io/packagist/v/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/last-fm-client-bundle.svg)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![License](https://poser.pugx.org/calliostro/last-fm-client-bundle/license)](https://packagist.org/packages/calliostro/last-fm-client-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![CI](https://github.com/calliostro/last-fm-client-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/calliostro/last-fm-client-bundle/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/calliostro/last-fm-client-bundle/graph/badge.svg?token=3ATEFYF7A0)](https://codecov.io/gh/calliostro/last-fm-client-bundle)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![Code Style](https://img.shields.io/badge/code%20style-PSR12-brightgreen.svg)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

A Symfony bundle integrating [`calliostro/lastfm-client`](https://github.com/calliostro/lastfm-client) into your Symfony application. Provides dependency injection, autowiring, built-in retry resilience, scrobbling, and optional rate limiting for PHP 8.1+ and Symfony 6.4, 7.x, and 8.x.

## 📦 Installation

Install via Composer:

```bash
composer require calliostro/lastfm-bundle
```

---

## ⚙️ Configuration

Configure the bundle in `config/packages/calliostro_lastfm.yaml`:

```yaml
calliostro_lastfm:
    # API credentials (get from https://www.last.fm/api/account/create)
    api_key: '%env(LASTFM_API_KEY)%'
    api_secret: '%env(LASTFM_SECRET)%'

    # Optional: Session key for authenticated user operations (scrobbling, loving tracks)
    # session_key: '%env(LASTFM_SESSION_KEY)%'

    # Optional: HTTP User-Agent header for API requests
    # user_agent: 'MyApp/1.0 +https://myapp.com'

    # Optional: Retry resilience settings (enabled by default)
    # auto_retry: true     # Automatically wait and retry on 429 and 503 responses (default: true)
    # max_retries: 3       # Maximum number of retry attempts (default: 3)

    # Optional: Proactive rate limiting (requires symfony/rate-limiter)
    # rate_limiter: lastfm_api
```

> [!NOTE]
> By default, the client uses `LastfmClient/2.1.0 (+https://github.com/calliostro/lastfm-client)` as User-Agent. You can override this in the configuration if needed.

### Authentication Credentials

- **API Key:** Required for all API requests. Obtain your API key from [Last.fm API Account Creation](https://www.last.fm/api/account/create).
- **API Secret:** Required for signed operations (scrobbling, loving tracks, now playing updates).
- **Session Key:** Required for user-specific actions. Obtain this via the [Last.fm Authentication Flow](https://www.last.fm/api/authentication) or mobile authentication.
- **Anonymous Access:** If no credentials are configured, the client provides limited access to unauthenticated public endpoints.

---

## 🚀 Quick Start

### Basic Usage

Inject the `LastFmClient` service directly into your controllers or services:

```php
<?php

namespace App\Controller;

use Calliostro\LastFm\LastFmClient;
use Symfony\Component\HttpFoundation\JsonResponse;

final class MusicController
{
    public function artistInfo(string $artist, LastFmClient $client): JsonResponse
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
// Scrobbling requires api_key, api_secret, and session_key
$client->scrobbleTrack(
    artist: 'The Weeknd',
    track: 'Blinding Lights',
    timestamp: time()
);

$client->loveTrack(artist: 'Olivia Rodrigo', track: 'good 4 u');

$recentTracks = $client->getUserRecentTracks(user: 'username', limit: 10);
$topArtists = $client->getUserTopArtists(user: 'username', period: '1month');
```

### Music Discovery

```php
$artistInfo = $client->getArtistInfo(artist: 'Billie Eilish');
$albumInfo = $client->getAlbumInfo(artist: 'Taylor Swift', album: 'Midnights');
$trackInfo = $client->getTrackInfo(artist: 'The Weeknd', track: 'Blinding Lights');

$similarArtists = $client->getSimilarArtists(artist: 'Olivia Rodrigo');
$topTracks = $client->getArtistTopTracks(artist: 'Dua Lipa', limit: 10);
$topAlbums = $client->getArtistTopAlbums(artist: 'Ariana Grande');
```

---

## ✨ Key Features

- **Lightweight Integration** – Minimal footprint with zero overhead on top of `calliostro/lastfm-client`.
- **Complete API Coverage** – All Last.fm API endpoints supported (Album, Artist, Chart, Geo, Library, Tag, Track, User).
- **Direct API Calls** – `$client->getArtistInfo(artist: 'name')` maps directly to Last.fm API methods.
- **Built-in Retry Resilience** – Automatic exponential backoff and retry handling for `429 Too Many Requests` and `503 Service Unavailable` responses.
- **Type Safe & IDE Support** – PHP 8.1+ types, named parameters, and PHPStan Level 8 static analysis.
- **Symfony Native** – Autowiring support for Symfony 6.4, 7.x, and 8.x.
- **Flexible Authentication** – API Key for read operations, API Secret and Session Key for user operations and scrobbling.

---

## 🎵 All Last.fm API Methods as Direct Calls

- **Album Methods** – `addAlbumTags()`, `getAlbumInfo()`, `getAlbumTags()`, `getAlbumTopTags()`, `removeAlbumTag()`, `searchAlbums()`
- **Artist Methods** – `addArtistTags()`, `getArtistCorrection()`, `getArtistInfo()`, `getSimilarArtists()`, `getArtistTags()`, `getArtistTopAlbums()`, `getArtistTopTags()`, `getArtistTopTracks()`, `removeArtistTag()`, `searchArtists()`
- **Chart Methods** – `getTopArtistsChart()`, `getTopTagsChart()`, `getTopTracksChart()`
- **Geography Methods** – `getTopArtistsByCountry()`, `getTopTracksByCountry()`
- **Library Methods** – `getLibraryArtists()`
- **Tag Methods** – `getTagInfo()`, `getSimilarTags()`, `getTagTopAlbums()`, `getTagTopArtists()`, `getTopTags()`, `getTagTopTracks()`, `getTagWeeklyChartList()`
- **Track Methods** – `addTrackTags()`, `getTrackCorrection()`, `getTrackInfo()`, `getSimilarTracks()`, `getTrackTags()`, `getTrackTopTags()`, `loveTrack()`, `removeTrackTag()`, `scrobbleTrack()`, `searchTracks()`, `unloveTrack()`, `updateNowPlaying()`
- **User Methods** – `getUserArtistTracks()`, `getUserFriends()`, `getUserInfo()`, `getUserLovedTracks()`, `getUserPersonalTags()`, `getUserRecentTracks()`, `getUserTopAlbums()`, `getUserTopArtists()`, `getUserTopTags()`, `getUserTopTracks()`, `getUserWeeklyAlbumChart()`, `getUserWeeklyArtistChart()`, `getUserWeeklyChartList()`, `getUserWeeklyTrackChart()`

> [!NOTE]
> Complete method documentation and endpoint parameters can be found in the [Last.fm API Documentation](https://www.last.fm/api).

---

## 📋 Requirements

- **PHP** `^8.1` (tested on PHP 8.1–8.6)
- **Symfony** `^6.4 || ^7.0 || ^8.0`
- **calliostro/lastfm-client** `^2.1`

---

## ⚡ Resilience & Rate Limiting

### Built-in Retries (Reactive)

Out of the box, `calliostro/lastfm-client` v2.1 automatically handles rate limit responses (`429 Too Many Requests`) and temporary service downtime (`503 Service Unavailable`). When triggered, the client respects the `Retry-After` header or uses exponential backoff before retrying the request.

You can customize or disable this behavior in `config/packages/calliostro_lastfm.yaml`:

```yaml
calliostro_lastfm:
    auto_retry: true   # default: true
    max_retries: 3     # default: 3
```

### Symfony Rate Limiter (Proactive, Optional)

For high-volume batch processing, background workers, or scraping tasks, use `symfony/rate-limiter` to throttle outgoing requests client-side before sending them:

```bash
composer require symfony/rate-limiter
```

#### 1. Configure the Rate Limiter

```yaml
# config/packages/rate_limiter.yaml
rate_limiter:
    lastfm_api:
        policy: 'sliding_window'
        limit: 5  # Last.fm allows up to 5 requests per second per IP
        interval: '1 second'
```

#### 2. Assign to the Bundle

```yaml
# config/packages/calliostro_lastfm.yaml
calliostro_lastfm:
    api_key: '%env(LASTFM_API_KEY)%'
    api_secret: '%env(LASTFM_SECRET)%'
    rate_limiter: lastfm_api
```

---

## 🧪 Development & Testing Guide

See [DEVELOPMENT.md](DEVELOPMENT.md) for detailed setup instructions, test suite commands, static analysis, and contribution guidelines.

---

## 🤝 Contributing

Contributions are welcome! Please ensure that all tests pass and coding standards are maintained:

```bash
composer cs-fix
composer analyse
composer test
```

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## ⚖️ Disclaimer

Last.fm is a registered trademark of CBS Interactive Inc. / Paramount Global. This project is an independent, unofficial open-source library and is not affiliated with, endorsed by, or sponsored by Last.fm or Paramount Global.

---

## 🙏 Acknowledgments

- [Last.fm](https://www.last.fm/) for providing the database and API.
- [Symfony](https://symfony.com) for the web framework and dependency injection container.
- Underlying client: [`calliostro/lastfm-client`](https://github.com/calliostro/lastfm-client).
- Sister Symfony bundles: [`calliostro/spotify-web-api-bundle`](https://github.com/calliostro/spotify-web-api-bundle), [`calliostro/discogs-bundle`](https://github.com/calliostro/discogs-bundle), and [`calliostro/musicbrainz-bundle`](https://github.com/calliostro/musicbrainz-bundle).
