# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0-beta.4](https://github.com/calliostro/lastfm-bundle/releases/tag/v2.0.0-beta.4) – 2025-11-13

### 🚀 Complete Rewrite — Fresh Start for Last.fm

This version represents a complete architectural rewrite with modern Symfony patterns. v2.0.0 is essentially a new bundle built for the Last.fm API.

### Added

- **API Key Authentication** for all Last.fm operations  
- **API Secret Support** for authenticated operations (used with Session Key for scrobbling and user operations)
- **Session Key Support** for authenticated user operations (scrobbling, now playing, user-specific data)
- **All Last.fm API Methods** with consistent naming and modern parameter style
- **Complete API Coverage** – Album, Artist, Auth, Chart, Geo, Library, Tag, Track, User methods
- **Named Parameter Support** – Methods accept individual parameters in camelCase instead of arrays
- **Symfony Rate Limiter Integration** – Optional advanced rate limiting with configurable policies
- **Symfony 6.4 | 7.x | 8.x Support** with future compatibility
- **Modern PHP 8.1+ Architecture** with full type safety and modern features
- **Comprehensive Test Suite** with unit and integration tests
- **Professional Documentation** with clear examples and setup guides
- **Modern Bundle Structure** following all Symfony best practices
- **Robust Configuration Validation** with meaningful error messages and setup instructions
- **Music Scrobbling Support** throughout documentation and examples

### Changed

- **Method Parameter Style** – All methods now accept individual parameters (e.g., `getArtistInfo(artist: 'name')`) instead of arrays
- **Method Names** adapted for Last.fm API patterns (e.g., `getArtistTopTracks()`, `scrobbleTrack()`)
- **Parameter Naming** – All parameters use camelCase convention (e.g., `artist` instead of `artistId`)
- **Rate Limiting** – Configured for Last.fm's 5 requests per second limit
- **Service Naming** follows modern Symfony conventions with proper aliases
- **Error Handling** improved with better exceptions and validation
- **Performance** optimized for modern PHP versions
- **Complete API Integration** now based on `calliostro/lastfm-client` v2.0.0-beta.1
- **Code Standards** fully compliant with @Symfony and @Symfony:risky rules

### Removed

- **Legacy Configuration** – Replaced with modern Last.fm specific options
- **Array Parameter Style** – Methods no longer accept parameter arrays
- **Legacy Dependencies** – Clean implementation based on modern Last.fm client
- **Backward Compatibility** – This is a fresh start adapted for Last.fm API

## [1.0.0](https://github.com/calliostro/lastfm-bundle/releases/tag/v1.0.0) – 2025-09-06

### Added

- **Complete rewrite**: Modern Symfony bundle with `calliostro/lastfm-client`
- **Enhanced configuration validation**: API key and secret validation
- **Improved service architecture**: Modern dependency injection patterns
- **Symfony Flex integration**: Automatic setup with `composer require`

### Breaking Changes

- **New dependency**: Migrated from `snapshotpl/last-fm-client` to `calliostro/lastfm-client`
- **Configuration changes**: Updated service configuration structure
- **Legacy support**: Version `^0.4.3` available on legacy/0.x branch

### Migration

- **From v0.4.x**: Complete rewrite requires migration (see README)
- **Legacy branch**: `legacy/0.x` maintained with the final version v0.4.3

---

## [0.4.3](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.4.3) – 2025-09-06 (Legacy Branch)

### Documentation

- **Fixed coverage badge link**: Corrected codecov.io URL for proper branch coverage display
- **Enhanced v1.0.0 migration guidance**: Added migration information to a modern version
- **Final legacy version**: Last update for `snapshotpl/last-fm-client` support

**Note**: This version is available on the `legacy/0.x` branch for existing projects.

---

## [0.4.2](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.4.2) – 2025-08-29

### Maintenance

- Continued maintenance and compatibility updates for `snapshotpl/last-fm-client`
- Enhanced documentation and examples

### Notes

- **Final version with `snapshotpl/last-fm-client` support**
- **Legacy maintenance mode**: Only PHP/Symfony compatibility updates, no new features
- **For new projects**: Use version `^1.0` with modern `calliostro/lastfm-client`
- **For existing projects**: Stay on `^0.4` if migration to v1.0 is not desired

---

## [0.4.1](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.4.1) – 2024-03-15

### Fixed

- Compatibility issues with Symfony 7.x
- Service autowiring improvements

---

## [0.4.0](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.4.0) – 2024-01-20

### New Features

- Support for Symfony 6.4, 7.x, and 8.0
- PHP 8.1+ requirement
- Enhanced type declarations and modern PHP features

### Compatibility Changes

- Minimum PHP version raised to 8.1
- Updated Symfony compatibility matrix

---

## [0.3.0](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.3.0) – 2023-11-10

### Features

- Support for Symfony 6.x
- Improved service configuration
- Better error handling

### Improvements

- Updated dependencies and compatibility

---

## [0.2.0](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.2.0) – 2023-06-15

### Enhancements

- Enhanced authentication flow examples
- Improved documentation
- Additional service configurations

### Bug Fixes

- Various dependency injection issues

---

## [0.1.1](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.1.1) – 2023-04-15

### Fixed

- Minor bug fixes and improvements
- Documentation updates

---

## [0.1.0](https://github.com/calliostro/lastfm-bundle/releases/tag/v0.1.0) – 2023-04-01

### Initial Release

- Basic Symfony bundle for Last.fm API integration
- Support for `snapshotpl/last-fm-client`
- Service autowiring and configuration
- Authentication flow support
