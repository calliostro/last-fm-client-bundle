# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] – 2025-08-29

### Added

- **Complete rewrite**: Modern Symfony bundle with `calliostro/lastfm-client`
- **Symfony Flex integration**: Automatic setup with `composer require`
- **Simplified configuration**: Environment-based setup

### Breaking Changes

- **New dependency**: Migrated from `snapshotpl/last-fm-client` to `calliostro/lastfm-client`
- **For legacy support**: Use version `^0.4` (legacy-0.x branch)

---

## [0.4.2] – 2025-08-29

### Maintenance

- Continued maintenance and compatibility updates for `snapshotpl/last-fm-client`
- Enhanced documentation and examples

### Notes

- **Final version with `snapshotpl/last-fm-client` support**
- **Legacy maintenance mode**: Only PHP/Symfony compatibility updates, no new features
- **For new projects**: Use version `^1.0` with modern `calliostro/lastfm-client`
- **For existing projects**: Stay on `^0.4` if migration to v1.0 is not desired

---

## [0.4.1] – 2024-03-15

### Fixed

- Compatibility issues with Symfony 7.x
- Service autowiring improvements

---

## [0.4.0] – 2024-01-20

### New Features

- Support for Symfony 6.4, 7.x, and 8.0
- PHP 8.1+ requirement
- Enhanced type declarations and modern PHP features

### Compatibility Changes

- Minimum PHP version raised to 8.1
- Updated Symfony compatibility matrix

---

## [0.3.0] – 2023-11-10

### Features

- Support for Symfony 6.x
- Improved service configuration
- Better error handling

### Improvements

- Updated dependencies and compatibility

---

## [0.2.0] – 2023-06-15

### Enhancements

- Enhanced authentication flow examples
- Improved documentation
- Additional service configurations

### Bug Fixes

- Various dependency injection issues

---

## [0.1.0] – 2023-04-01

### Initial Release

- Basic Symfony bundle for Last.fm API integration
- Support for `snapshotpl/last-fm-client`
- Service autowiring and configuration
- Authentication flow support
